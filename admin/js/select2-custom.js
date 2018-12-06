(function( $ ) {
	'use strict';

	$('.mu-meta-post-selector').select2( {
		placeholder: 'Select a post',
        language: 'zh-TW',
        width: '100%',
        // 最多字元限制
        maximumInputLength: 10,
        // 最少字元才觸發尋找, 0 不指定
        minimumInputLength: 2,
        // 當找不到可以使用輸入的文字
        // tags: true,
		ajax: {
			url: ajaxurl,
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term,
					action: 'mu_meta_post_selector_lookup',
					post_type: $(this).attr('data-post-type'),
					mu_meta_post_selector_field_id: $(this).attr('data-mu-meta-post-selector-field-id')
				};
			},
			processResults: function (data, params) {
				params.page = params.page || 1;
				if (typeof(data.results) == 'undefined') {
					data.results = [];
				}
				return {
					results: data.results,
					pagination: {
						more: (params.page * 10) < data.count_filtered
					}
				};
			}
		}

	});

})( jQuery );
