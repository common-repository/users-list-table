
jQuery.noConflict();

(function( $ ) {

	var users_current_role = '';
	var users_current_orderby = '';
	var users_current_order = '';
	var users_current_page = 1;
	var users_current_pages = 1;

	$(function() {

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * $(function() {
	 *
	 * });
	 * 
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

		// Makes request after page selector changed.
		$('.tablenav-pages select').on('change', function(){

			$('.tablenav-pages select').val($(this).val());
			load_users_list_ajax(users_current_role, users_current_orderby, users_current_order, $(this).val());
		});
		

		// Changes page number.
		$('.tablenav-pages .prev-page').on('click', function(){
			if( $(this).attr('disabled') ){
				return false;
			}
			users_current_page -= 1;
		});

		// Changes page number.
		$('.tablenav-pages .next-page').on('click', function(){
			if( $(this).attr('disabled') ){
				return false;
			}
			users_current_page += 1;
		});


		// Makes request after paginator navigation clicked.
		$('.tablenav-pages, .tablenav-pages .prev-page, .tablenav-pages .next-page, .tablenav-pages').on('click', function(){

			if( $(this).attr('disabled') ){
				return false;
			}

			$('.tablenav-pages select').val(users_current_page);
			load_users_list_ajax(users_current_role, users_current_orderby, users_current_order, users_current_page);
			return false;
		});

		// Makes request after role navigation clicked.
		$('a[data-filter-role]').click(function(){

			$('.users-roles-nav a').removeClass('current');
			$(this).addClass('current');

			load_users_list_ajax($(this).attr('data-filter-role'), users_current_orderby, users_current_order, 1);
			return false;
		});

		// Makes request after role navigation clicked.
		$('a[data-sort-orderby]').click(function(){

			$(this).blur();

			// If order by field changes to another field, then we should set default order type.
			if( $(this).parent('th').hasClass('sortable') ){

				$('.users-list-table th.sorted')
					.removeClass('sorted asc')
					.addClass('sortable desc');

				$(this).attr('data-sort-order', 'asc');
			}

			load_users_list_ajax(users_current_role, $(this).attr('data-sort-orderby'), $(this).attr('data-sort-order'), users_current_page);

			// Set new order type for the next request.
			var new_order_dir = $(this).attr('data-sort-order') === 'asc' ? 'desc' : 'asc';
			$(this).attr('data-sort-order', new_order_dir);

			$(this).parent('th')
				.removeClass('sortable')
				.addClass('sorted')
				.toggleClass('asc desc');
		
			return false;
		
		});
		
		/**
		 * Makes an ajax query with filter/sorting options.
		 *
		 * @param role
		 * @param orderby
		 * @param order
		 * @param paged
		 * @returns {boolean}
		 */
		function load_users_list_ajax( role, orderby, order, paged ){

			var postData = {
				'action': 'load_users_list',
				'role': role,
				'orderby': orderby,
				'order': order,
				'paged': paged
			};
		
			// Set passed arguments as current query options
			users_current_role = role;
			users_current_orderby = orderby;
			users_current_order = order;
			users_current_page = parseInt(paged);
		
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: users_list.ajaxurl,
				data: postData,
				success: function( res ){
					
					var container = $('#list_table_body');
					container.html('');
					if( !res || !res.all_user_elements || !res.user_elements.length ){
						var row = $($('#user_table_noresults').html());
						container.append(row);
						return;
					}
		
					users_current_pages = res.total_pages;
		
					// Create table row for each result item
					$.each(res.user_elements, function( i, item ){
						var row = $($('#user_table_row').html());

						row.find('#user_avatar').html(item.user_avatar);
						row.find('#user_name_link').text(item.user_name).prop('href', item.user_link);
						row.find('#display_name').html(item.display_name);
						row.find('#user_email').text(item.user_email);
						row.find('#user_roles').html(item.user_roles);
						container.append(row);
					})
		
					// If total page number changed, regenerate select options
					if( res.total_pages && $('.tablenav-pages select:first option').length !== res.total_pages ){

						$('.tablenav-pages select').empty();
						for(var x = 1; x <= parseInt(res.total_pages); x++){
							$('.tablenav-pages select').append($('<option/>').prop('value', x).text(x));
						}
					}
		
					// Disable/Enable previous page status depending on current page
					if( users_current_page <= 1 ){
						$('.tablenav-pages .prev-page').attr('disabled', 'disabled');
					} else{
						$('.tablenav-pages .prev-page').removeAttr('disabled');
					}
		
					// Disable/enable next page link depending on current page & total pages
					if( users_current_page + 1 > res.total_pages ){
						$('.tablenav-pages .next-page').attr('disabled', 'disabled');
					} else{
						$('.tablenav-pages .next-page').removeAttr('disabled');
					}
		
					// Change labels to new total found count
					$('.tablenav-pages .total-pages').text(res.total_pages_formatted);
		
					// Hide paginator if only one page.
					if( res.total_pages == 1 ){
						$('.tablenav-pages').addClass('one-page');
					} else{
						$('.tablenav-pages').removeClass('one-page');
					}
				},
			});
			return false;
		}
	});
})( jQuery );
