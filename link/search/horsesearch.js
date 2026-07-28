jQuery(document).ready(function($) {
    var $ftShow = $('#ht-show');
    var postTypes = $ftShow.data('types').split(',');
    var postLabels = $ftShow.data('labels').split(',');
    var postJson = atob($ftShow.data('json'));
    var postFull = $ftShow.data('full');
    var postNone = $ftShow.data('none');

    var resultData = {};
    var countData = {};
    postTypes.forEach(function(type) {
        resultData[type + 'Results'] = '';
        countData[type + 'Count'] = 0;
    });

    var postLimit = postFull;
    var debounceTimer;
    var popupOpened = false;
    var cachedData = null; // Biến lưu trữ dữ liệu JSON đã fetch khi popup mở

    // Thêm các span vào #ht-staxo dựa trên postTypes và mặc định chọn tất cả
    postTypes.forEach(function(type) {
        var typeLabel = postLabels[postTypes.indexOf(type)];
        $('#ht-staxo').append(`<span class="postType-filter active" data-type="${type}">${typeLabel}</span>`);
    });

    var selectedTypes = new Set(postTypes);

    $('#ht-staxo').on('click', '.postType-filter', function() {
        var selectedType = $(this).data('type');
        $(this).toggleClass('active');
        if ($(this).hasClass('active')) {
            selectedTypes.add(selectedType);
        } else {
            selectedTypes.delete(selectedType);
        }
        filterResultsBySelectedTypes();
    });

    function filterResultsBySelectedTypes() {
        var combinedResults = '';
        var hasResults = false;

        if (selectedTypes.size === 0) {
            $('#ht-show').html(`<li>${postNone}</li>`);
            return;
        }

        selectedTypes.forEach(function(type) {
            if (resultData[type + 'Results']) {
                combinedResults += `<li class="ht-stit">${postLabels[postTypes.indexOf(type)]}</li>${resultData[type + 'Results']}`;
                hasResults = true;
            }
        });

        if (hasResults) {
            $('#ht-show').html(combinedResults);
        } else {
            $('#ht-show').html(`<li>${postNone}</li>`);
        }
    }

    function syncInputs(sourceInput) {
        var searchText = sourceInput.val();
        $('input[name="s"]').not(sourceInput).val(searchText);
    }

    $('.htopensearch').on('click', function() {
        if (!popupOpened) {
            $('#ht-sbox').modal({ fadeDuration: 200 });
            $('.jquery-modal.blocker').addClass('horse-blocker');
            popupOpened = true;

            // Khi mở popup, fetch và lưu dữ liệu vào cachedData
            if (cachedData === null) {
                fetch(postJson)
                    .then(response => response.json())
                    .then(data => {
                        cachedData = data; // Lưu trữ dữ liệu JSON vào biến cachedData
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            setTimeout(function() { $('#ht-sinput').focus(); }, 500);
        }
    });

    $('input[name="s"]').on('input', function() {
        if (!popupOpened) {
            $('#ht-sbox').modal({ fadeDuration: 200 });
            $('.jquery-modal.blocker').addClass('horse-blocker');
            popupOpened = true;

            if (cachedData === null) {
                fetch(postJson)
                    .then(response => response.json())
                    .then(data => {
                        cachedData = data;
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            setTimeout(function() { $('#ht-sinput').focus(); }, 500);
        }

        syncInputs($(this));

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            var searchText = $('input[name="s"]').val();
            if (searchText.length >= 1) {
                displayResults(cachedData, searchText); // Sử dụng dữ liệu từ cachedData
            } else {
                $('#ht-show').empty().removeClass('ht-showbg');
            }
        }, 100);
    });

    $('#ht-sinput').on('input', function() {
        syncInputs($(this));

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            var searchText = $('#ht-sinput').val();
            if (searchText.length >= 1) {
                displayResults(cachedData, searchText); // Sử dụng dữ liệu từ cachedData
            } else {
                $('#ht-show').empty().removeClass('ht-showbg');
            }
        }, 100);
    });

    // Sự kiện đóng modal khi người dùng đóng popup
    $(document).on('modal:close', '#ht-sbox', function() {
        popupOpened = false;
    });

    // Xóa cache khi tải lại trang
    $(window).on('beforeunload', function() {
        cachedData = null; // Xóa cache khi người dùng tải lại trang
    });

    function removeDiacritics(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/Đ/g, "D");
    }

    function displayResults(data, searchText) {
        var combinedResults = '';
        var hasResults = false;

        postTypes.forEach(function(type) {
            resultData[type + 'Results'] = '';
            countData[type + 'Count'] = 0;
        });

        if (data && data.length > 0) {
            $('#ht-show').addClass('ht-showbg');

            var searchWords = searchText.split(/\s+/).map(word => removeDiacritics(word.toLowerCase()));

            data.forEach(function(item) {
                var title = item.title || '';
                var normalizedTitle = removeDiacritics(title).toLowerCase();

                var matchFound = searchWords.every(word => normalizedTitle.includes(word));

                if (matchFound) {
                    var type = item.type;
                    if (postTypes.includes(type) && countData[type + 'Count'] < postLimit) {
                        var textHighlight = highlightSearchText(title, searchWords);
                        var url = item.url;
                        var thum = item.thum;
                        var pri = item.price || '';
                        var taxo = item.taxonomy || '';
                        var itemHTML = thum ? 
                            `<li class="ht-ssp"><a href="${url}"><img src="${thum}"></a><a href="${url}"><span class="ht-ssap-tit">${textHighlight}</span><span class="ht-ssap-cm">${taxo}</span><span class="ht-ssap-pri">${pri}</span></a></li>` : 
                            `<li class="ht-sspno"><a href="${url}"><span class="ht-ssap-tit">${textHighlight}</span><span class="ht-ssap-cm">${taxo}</span><span class="ht-ssap-pri">${pri}</span></a></li>`;

                        resultData[type + 'Results'] += itemHTML;
                        countData[type + 'Count']++;
                        hasResults = true;
                    }
                }
            });

            filterResultsBySelectedTypes();
        }
        if (!hasResults) {
            $('#ht-show').html(`<li>${postNone}</li>`);
        }
    }

    function highlightSearchText(text, searchWords) {
        var regex = new RegExp(searchWords.join('|'), 'gi');
        return text.replace(regex, match => `<span class="ht-sselec">${match}</span>`);
    }

    $('#ht-sclose').on('click', function() {
        popupOpened = false;
    });
});
