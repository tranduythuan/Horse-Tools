<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_gindex_options;
function horsetools_require_google_api_index() {
    require_once( HORSETOOLS_DIR . 'link/google-api/vendor/autoload.php');
}
// check link eror
function horsetools_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
// quan ly json index api
function horsetools_jsonapi_index() {
    global $horsetools_gindex_options;
    horsetools_require_google_api_index();
    $jsonStrings = array(); 
    if (is_array($horsetools_gindex_options) || is_object($horsetools_gindex_options)) {
        foreach ($horsetools_gindex_options as $key => $value) {
            if (preg_match('/^json(\d+)$/', $key, $matches)) {
                $n = $matches[1];
                $jsonStrings[] = sanitize_text_field($value);
            }
        }
    }
    if (empty($jsonStrings)) {
        return false; 
    }
    $randomIndex = array_rand($jsonStrings);
    $selectedJsonString = $jsonStrings[$randomIndex];
    $jsonObject = json_decode($selectedJsonString, true);
    if ($jsonObject === null) {
        return false;
    }
    $client = new \Google\Client();
    $client->setAuthConfig($jsonObject);
    $client->addScope('https://www.googleapis.com/auth/indexing');
    return $client->authorize();
}
// xu ly index now and del index
function horsetools_index_now($urls, $action) {
    $result = [];
    $type = $action == 'delete' ? 'URL_DELETED' : 'URL_UPDATED';
    $httpClient = horsetools_jsonapi_index();
	
	if (!$httpClient) {
        $result[] = array(
            'result' => 'error',
            'error' => 'Failed to initialize Google API client'
        );
        return $result;
    }
	
    foreach ($urls as $url) {
        $data = [
            'result' => 'success'
        ];
        if (!horsetools_valid_url($url)) {
            $data['result'] = 'error';
            $data['error'] = 'Invalid URL: ' . $url;
            $result[] = $data;
            continue;
        }
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
        try {
            if ($action == 'get') {
                $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($url));
            } else {
                $content = json_encode([
                    'url' => $url,
                    'type' => $type
                ]);
                $response = $httpClient->post($endpoint, ['body' => $content]);
            }
            $data['body'] = (string) $response->getBody();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // This used to be `catch (ClientException $e)` with no import, so
            // it resolved to a non-existent \ClientException and never matched
            // — every Guzzle error fell through to the generic handler and the
            // HTTP status code (403 quota, 404 not owned, 429 rate limit) was
            // lost from the message shown to the user.
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $data['result'] = 'error';
            $data['error'] = $statusCode
                ? 'HTTP Error ' . $statusCode . ': ' . $e->getMessage()
                : $e->getMessage();
        } catch (\Exception $e) {
            $data['result'] = 'error';
            $data['error'] = $e->getMessage();
        }
        $result[] = $data;
    }
    return $result;
}
// Xử lý ajax index now and del index
function horsetools_index_now_callback() {
    if ( ! current_user_can('manage_options') ) { wp_send_json_error('forbidden', 403); }
    if (!wp_verify_nonce($_POST['ajax_nonce'], 'horsetools_index_now_nonce')) {
        wp_die('Invalid nonce');
    }
    $urls = explode("\n", sanitize_textarea_field( wp_unslash( $_POST['url'] ?? '' ) ));
    $action = isset($_POST['ajax_action']) ? sanitize_key( wp_unslash($_POST['ajax_action']) ) : '';
    if (!in_array($action, array('update', 'delete', 'get'), true)) {
        wp_send_json_error('invalid action', 400);
    }
    // Chia nhỏ mảng URL thành các phần có tối đa 200 URL mỗi phần
    $chunks = array_chunk($urls, 200);
    // Duyệt qua từng phần và xử lý
    foreach ($chunks as $chunk) {
        $result = horsetools_index_now($chunk, $action);
        foreach ($result as $item) {
            if (array_key_exists('body', $item)) {
                $data = json_decode($item['body'], true);
                if ($data && isset($data['urlNotificationMetadata'])) {
                    $latest_update = $data['urlNotificationMetadata']['latestUpdate'] ?? null;
                    $latest_remove = $data['urlNotificationMetadata']['latestRemove'] ?? null;
                    // Xác định trạng thái dựa trên dữ liệu
                    if ($latest_update || $latest_remove) {
                        $latest_update_time = strtotime($latest_update['notifyTime'] ?? '');
                        $latest_remove_time = strtotime($latest_remove['notifyTime'] ?? '');
                        if ($latest_update_time > $latest_remove_time) {
                            $url = $data['urlNotificationMetadata']['url'];
                            $status = __('Already declared', 'horse-tools');
                            $time = wp_date('Y-m-d H:i:s', $latest_update_time);
                            $class = '';
                        } else {
                            $url = $data['urlNotificationMetadata']['url'];
                            $status = __('Declaration already deleted', 'horse-tools');
                            $time = wp_date('Y-m-d H:i:s', $latest_remove_time);
                            $class = 'ht-index-del';
                        }
                        // Hiển thị thông tin
                        echo '<div class="ht-index '. $class .'">';
                        echo __('URL:', 'horse-tools') .' '. esc_html($url) .'<br>';
                        echo __('Status:', 'horse-tools') .' '. $status .'<br>';
                        echo __('Time:', 'horse-tools') .' '. $time;
                        echo '</div>';
                        horsetools_index_use_count(); // count user
                    } else {
                        // Thông báo nếu chỉ có 'success' mà không có chi tiết update/remove
                        echo '<div class="ht-index">';
                        echo __('Notification link sent', 'horse-tools');
                        echo '</div>';
                    }
                } else {
                    // Xử lý nếu dữ liệu trả về không hợp lệ hoặc không có thông tin
                    echo '<div class="ht-index ht-index-er">';
                    echo __('URL: does not exist', 'horse-tools');
                    echo '</div>';
                }
            } else {
                // Xử lý khi thiếu khóa 'body'
                echo '<div class="ht-index ht-index-er">';
                echo __('URL: does not exist', 'horse-tools');
                echo '</div>';
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_horsetools_index_now_ajax', 'horsetools_index_now_callback');

// xy ly index status
function horsetools_index_status($urls) {
    $result = [];
    $httpClient = horsetools_jsonapi_index(); 
    foreach ($urls as $url) {
        $data = [
            'url' => $url,
            'indexed' => null,
            'latest_update' => null, 
            'latest_remove' => null, 
        ];
        if (!horsetools_valid_url($url)) {
            $data['result'] = 'error';
            $data['error'] = 'Invalid URL: ' . $url;
            $result[] = $data;
            continue;
        }
        try {
            $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($url));
            $response_body = json_decode($response->getBody(), true);
            if (isset($response_body['latestUpdate'])) {
                $data['latest_update'] = $response_body['latestUpdate'];
            }
            if (isset($response_body['latestRemove'])) {
                $data['latest_remove'] = $response_body['latestRemove'];
            }
            if (isset($response_body['latestUpdate']) || isset($response_body['latestRemove'])) {
                $data['indexed'] = 'yes'; 
            } else {
                $data['indexed'] = 'no'; 
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                $data['indexed'] = null;
            } else {
                $data['indexed'] = 'error'; 
                $data['error'] = 'HTTP Error ' . $statusCode . ': ' . $e->getMessage();
            }
        } catch (\Exception $e) {
            $data['indexed'] = 'error';
            $data['error'] = $e->getMessage();
        }
        $result[] = $data;
    }
    return $result;
}
// ajax index status
function horsetools_index_status_callback() {
    if ( ! current_user_can('manage_options') ) { wp_send_json_error('forbidden', 403); }
    if (!wp_verify_nonce($_POST['ajax_nonce'], 'horsetools_index_status_nonce')) {
        wp_die('Invalid nonce');
    }
    $urls = explode("\n", sanitize_textarea_field( wp_unslash( $_POST['url'] ?? '' ) ));
    // Chia nhỏ mảng URL thành các phần có tối đa 200 URL mỗi phần
    $chunks = array_chunk($urls, 200);
    // Duyệt qua từng phần và xử lý
    foreach ($chunks as $chunk) {
        $url_metadata = horsetools_index_status($chunk); 
        foreach ($url_metadata as $data) {
            $url = isset($data['url']) ? $data['url'] : '';
            $latest_update = isset($data['latest_update']) ? $data['latest_update'] : null;
			$latest_remove = isset($data['latest_remove']) ? $data['latest_remove'] : null;
            if ($data['indexed'] == 'yes') {
                if ($latest_update || $latest_remove) {
                    $latest_update_time = !empty($latest_update['notifyTime']) ? strtotime($latest_update['notifyTime']) : null;
					$latest_remove_time = !empty($latest_remove['notifyTime']) ? strtotime($latest_remove['notifyTime']) : null;
                    if ($latest_update_time > $latest_remove_time) {
                        $status = __('Already declared', 'horse-tools');
                        $time = wp_date('Y-m-d H:i:s', $latest_update_time);
                        $class = '';
                    } else {
                        $status = __('Declaration already deleted', 'horse-tools');
                        $time = wp_date('Y-m-d H:i:s', $latest_remove_time);
                        $class = 'ht-index-del';
                    }
                }
                // Hiển thị thông tin
                echo '<div class="ht-index '. $class .'">';
                echo __('URL:', 'horse-tools') .' '. esc_html($url) .'<br>';
                echo __('Status:', 'horse-tools') .' '. $status .'<br>';
                echo __('Time:', 'horse-tools') .' '. $time;
                echo '</div>';
                horsetools_index_use_count(); // count user
            } else {
                echo '<div class="ht-index ht-index-er">';
                echo __('URL: Unverified', 'horse-tools');
                echo '</div>';
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_horsetools_index_status_ajax', 'horsetools_index_status_callback');

// ham cap nhat count
function horsetools_index_use_count() {
    $count = get_transient('horsetools_index_count');
    if (false === $count) {
        $count = 0;
    }
    $count++;
    set_transient('horsetools_index_count', $count, 86400);
}
// tao nut index
function horsetools_quick_index_button($actions, $post) {
	global $horsetools_gindex_options;
	if (isset($horsetools_gindex_options['posttype']) && in_array($post->post_type, $horsetools_gindex_options['posttype'])) {
		if ($post->post_status == 'publish') {
			$actions['index'] = sprintf(
				'<a id="horseindex-%1$s" class="horseindex-btn" data-id="%1$s" data-link="%2$s" href="#" rel="permalink">%3$s</a>',
				$post->ID,
				esc_url(get_permalink($post->ID)),
				__('Index now', 'horse-tools')
			);
		}
	}
    return $actions;
}
if(isset($horsetools_gindex_options['posttype'])){
	$main_search_post_types = $horsetools_gindex_options['posttype'];
	foreach ($main_search_post_types as $post_type) {
		$hook_name = $post_type .'_row_actions';
		add_filter($hook_name, 'horsetools_quick_index_button', 10, 2);
		add_filter($hook_name, 'horsetools_quick_index_button', 10, 2);
	}
}
// index ajax o edit
function horsetools_index_post_ajax() {
    if ( ! current_user_can('edit_posts') ) { wp_send_json_error('forbidden', 403); }
    if (!wp_verify_nonce($_POST['nonce'], 'horsetools_index_now_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    $post_id = isset($_POST['post_id']) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
    if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( 'forbidden', 403 );
    }
    // Derive the URL server-side. $_POST['url'] used to be submitted alongside
    // $post_id and sent straight to Google, while $post_id was validated and
    // then never used — so any Contributor could spend the site's Indexing API
    // quota on arbitrary URLs.
    $url = get_permalink( $post_id );
    if ( ! $url ) {
        wp_send_json_error('Invalid Post ID');
    }
    $result = horsetools_index_now([$url], 'update');
    if (!empty($result[0]['error'])) {
        wp_send_json_error($result[0]['error']);
    }
	horsetools_index_use_count(); // count user
    wp_send_json_success(__('Success', 'horse-tools'));
}
add_action('wp_ajax_horsetools_index_post', 'horsetools_index_post_ajax');
// js xu ly index
function horsetools_enqueue_scripts() {
    // Chi in nonce cho user co quyen dung nut index, tranh lo nonce cho subscriber.
    if ( ! current_user_can('edit_posts') ) { return; }
    wp_enqueue_script('jquery');
    ?>
    <script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.horseindex-btn').on('click', function(e) {
			e.preventDefault();
			var postId = $(this).data('id');
			var url = $(this).data('link');
			var button = $(this);
			$.ajax({
				url: ajaxurl, 
				method: 'POST',
				data: {
					action: 'horsetools_index_post',
					post_id: postId,
					url: url,
					nonce: '<?php echo wp_create_nonce('horsetools_index_now_nonce'); ?>'
				},
				beforeSend: function() {
					button.text('<?php _e('Wait...', 'horse-tools'); ?>'); 
				},
				success: function(response) {
					if (response.success) {
						button.text('<?php _e('Success', 'horse-tools'); ?>'); // Thành công
					} else {
						button.text('<?php _e('Error', 'horse-tools'); ?>'); 
					}
				},
				error: function() {
					button.text('<?php _e('Error', 'horse-tools'); ?>');
				}
			});
		});
	});
    </script>
    <?php
}
add_action('admin_footer', 'horsetools_enqueue_scripts');
