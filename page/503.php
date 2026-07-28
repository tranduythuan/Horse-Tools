<?php if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $horsetools_redirects_options;
$title = !empty($horsetools_redirects_options['redi31']) ? $horsetools_redirects_options['redi31'] : __('MAINTENANCE MODE', 'horse-tools');
$content = !empty($horsetools_redirects_options['redi32']) ? $horsetools_redirects_options['redi32'] : __('We apologize, the website is currently undergoing maintenance. Please wait for a moment', 'horse-tools');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( $content ) ); ?>">
    <title>503 - <?php echo esc_html( wp_strip_all_tags( $title ) ); ?></title>
    <style>
        /* No webfont import here on purpose. This page is served to every
           visitor while the site is down, and pulling a font from a third
           party would leak each visitor's IP and user agent to that host —
           which the plugin promises not to do. System stack instead. */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            color: #333;
			text-align: center;
        }
        .page_503 {
			background: #fff;
			height: 100vh;
			display: flex;
			justify-content: center;
			align-items: center;
			flex-direction: column;
			padding: 20px;
		}
		.container {
			max-width: 700px;
			width: 100%;
		}
        .four_zero_four_bg {
            background-image: url('<?php echo esc_url(HORSETOOLS_URL . 'img/503.gif'); ?>');
            height: 350px;
            background-position: center;
        }
        .four_zero_four_bg h1 {
            font-size: 80px;
			margin-top:0px;
			color:#95684a;
        }
        .contant_box_503 p{
            font-size: 25px;
        }
        .link_503 {
            color: #fff;
            background: #39ac31;
            padding: 10px 20px;
            margin: 20px 0;
            display: inline-block;
            text-decoration: none;
        }
        .link_503:hover {
            color: #fff;
            background: #333;
        }
    </style>
</head>
<body>
    <section class="page_503">
        <div class="container">
            <div class="row">    
                <div class="col-sm-12">
                    <div class="col-sm-10 col-sm-offset-1 text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center">503</h1>
                        </div>
                        <div class="contant_box_503">
                            <h2 class="h2"><?php echo esc_html( $title ); ?></h2>
                            <p><?php echo wp_kses_post( $content ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
