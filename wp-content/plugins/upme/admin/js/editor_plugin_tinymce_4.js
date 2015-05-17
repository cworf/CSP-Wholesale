(function () {
	"use strict";

	var UPMEShortcodesManager = function(editor, url) {

		editor.addButton('upme_shortcodes_button', function() {
			return {
				title: '',
				image: url,
				type: 'menubutton',
                icon : 'upme_shortcodes_button',
				menu: [
					{
						text: UPMETmce.LoginRegistrationForms,
						menu: [
							{
								text: UPMETmce.FrontRegistrationForm,
								onclick: function(){
									editor.insertContent('[upme_registration]');
								}
							},
							{
								text: UPMETmce.RegFormCustomRedirect,
								onclick: function(){
									editor.insertContent('[upme_registration redirect_to="http://url_here"]');
								}
							},
							{
								text: UPMETmce.RegFormCaptcha,
								onclick: function(){
									editor.insertContent('[upme_registration captcha=yes]');
								}
							},
							{
								text: UPMETmce.RegFormNoCaptcha,
								onclick: function(){
									editor.insertContent('[upme_registration captcha=no]');
								}
							},
							{
								text: UPMETmce.FrontLoginForm,
								onclick: function(){
									editor.insertContent('[upme_login]');
								}
							},
							{
								text: UPMETmce.SidebarLoginWidget,
								onclick: function(){
									editor.insertContent('[upme_login use_in_sidebar=yes]');
								}
							},
							{
								text: UPMETmce.LoginFormCustomRedirect,
								onclick: function(){
									editor.insertContent('[upme_login redirect_to="http://url_here"]');
								}
							},
							{
								text: UPMETmce.LogoutButton,
								onclick: function(){
									editor.insertContent('[upme_logout]');
								}
							},
							{
								text: UPMETmce.LogoutButtonCustomRedirect,
								onclick: function(){
									editor.insertContent('[upme_logout redirect_to="http://url_here"]');
								}
							},
						]
					},
					{
						text: UPMETmce.SingleProfile,
						menu: [
							{
								text: UPMETmce.LoggedUserProfile,
								onclick: function(){
									editor.insertContent('[upme]');
								}
							},
							{
								text: UPMETmce.LoggedUserProfileUserID,
								onclick: function(){
									editor.insertContent('[upme show_id=true]');
								}
							},
							{
								text: UPMETmce.PostAuthorProfile,
								onclick: function(){
									editor.insertContent('[upme id=author]');
								}
							},
							{
								text: UPMETmce.SpecificUserProfile,
								onclick: function(){
									editor.insertContent('[upme id=X]');
								}
							},
							{
								text: UPMETmce.LoggedUserProfileHideStats,
								onclick: function(){
									editor.insertContent('[upme show_stats=no]');
								}
							},
							{
								text: UPMETmce.LoggedUserProfileUserRole,
								onclick: function(){
									editor.insertContent('[upme show_role=yes]');
								}
							},
							{
								text: UPMETmce.LoggedUserProfileStatus,
								onclick: function(){
									editor.insertContent('[upme show_profile_status=yes]');
								}
							},
							{
								text: UPMETmce.LoggedUserProfileLogoutRedirect,
								onclick: function(){
									editor.insertContent('[upme logout_redirect=http://url_here]');
								}
							},
						]
					},
					{
						text: UPMETmce.MultipleProfilesMemberList,
						menu: [
							{
								text: UPMETmce.GroupSpecificUsers,
								onclick: function(){
									editor.insertContent('[upme group=user_id1,user_id2,user_id3,etc]');
								}
							},
							{
								text: UPMETmce.AllUsers,
								onclick: function(){
									editor.insertContent('[upme group=all users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersCompactView,
								onclick: function(){
									editor.insertContent('[upme group=all view=compact users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersCompactViewHalfWidth,
								onclick: function(){
									editor.insertContent('[upme group=all view=compact width=2 users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersModalWindow,
								onclick: function(){
									editor.insertContent('[upme group=all view=compact modal=yes users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersNewWindow,
								onclick: function(){
									editor.insertContent('[upme group=all view=compact new_window=yes users_per_page=10]');
								}
							},
							{
								text: UPMETmce.UsersBasedUserRole,
								onclick: function(){
									editor.insertContent('[upme group=all role=subscriber users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AdministratorUsersOnly,
								onclick: function(){
									editor.insertContent('[upme group=all role=administrator users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersOrderedDisplayName,
								onclick: function(){
									editor.insertContent('[upme group=all order=asc orderby=display_name users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersOrderedPostCount,
								onclick: function(){
									editor.insertContent('[upme group=all order=desc orderby=post_count users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersOrderedRegistrationDate,
								onclick: function(){
									editor.insertContent('[upme group=all order=desc orderby=registered users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersOrderedCustomField,
								onclick: function(){
									editor.insertContent('[upme group=all order=desc orderby=custom_field_meta_key orderby_custom=yes users_per_page=10]');
								}
							},
							{
								text: UPMETmce.AllUsersUserID,
								onclick: function(){
									editor.insertContent('[upme group=all show_id=true users_per_page=10]');
								}
							},
							{
								text: UPMETmce.GroupUsersCustomField,
								onclick: function(){
									editor.insertContent('[upme group=all  group_meta=custom_field_key group_meta_value=custom_field_value users_per_page=10]');
								}
							},
							{
								text: UPMETmce.HideUsersUntilSearch,
								onclick: function(){
									editor.insertContent('[upme group=all users_per_page=10 hide_until_search=true]');
								}
							},
							{
								text: UPMETmce.SearchProfile,
								onclick: function(){
									editor.insertContent('[upme_search operator=OR]');
								}
							},
							{
								text: UPMETmce.SearchCustomFieldFilters,
								onclick: function(){
									editor.insertContent('[upme_search filters=meta1,meta2,meta3]');
								}
							},
							
						]
					},
					{
						text: UPMETmce.PrivateContentLoginRequired,
						menu: [
							{
								text: UPMETmce.PrivateContentLoginRequired,
								onclick: function(){
									editor.insertContent('[upme_private]Place member only content here[/upme_private]');
								}
							},
							
						]
					},
					{
						text: UPMETmce.ShortcodeOptionExamples,
						menu: [
							{
								text: UPMETmce.HideUserStatistics,
								onclick: function(){
									editor.insertContent('[upme show_stats=no]');
								}
							},
							{
								text: UPMETmce.HideUserSocialBar,
								onclick: function(){
									editor.insertContent('[upme show_social_bar=no]');
								}
							},
							{
								text: UPMETmce.HalfWidthProfileView,
								onclick: function(){
									editor.insertContent('[upme width=2]');
								}
							},
							{
								text: UPMETmce.CompactViewNoExtraFields,
								onclick: function(){
									editor.insertContent('[upme view=compact]');
								}
							},
							{
								text: UPMETmce.CustomizedProfileFields,
								onclick: function(){
									editor.insertContent('[upme view=meta_id1,meta_id2,meta_id3]');
								}
							},
							{
								text: UPMETmce.ShowUserIDProfiles,
								onclick: function(){
									editor.insertContent('[upme show_id=true]');
								}
							},
							{
								text: UPMETmce.LimitResultsMemberList,
								onclick: function(){
									editor.insertContent('[upme group=all view=compact limit_results=yes users_per_page=4 ]');
								}
							},
							{
								text: UPMETmce.ShowResultCountMemberList,
								onclick: function(){
									editor.insertContent('[upme group=all view=compact users_per_page=10 show_result_count=yes  ]');
								}
							},
							
						]
					},
					
				]
			}
		});
	};
	
	tinymce.PluginManager.add( "UPMEShortcodes", UPMEShortcodesManager );
})();
