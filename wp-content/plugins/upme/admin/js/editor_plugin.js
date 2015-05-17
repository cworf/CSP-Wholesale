(
	function(){
	
		tinymce.create(
			"tinymce.plugins.UPMEShortcodes",
			{
				
				init: function(d,e) {},
				createControl:function(d,e)
				{
				
					if(d=="upme_shortcodes_button"){
					
						d=e.createMenuButton( "upme_shortcodes_button",{
							title:UPMETmce.InsertUPMEShortcode,
							icons:false
							});
							
							var a=this;d.onRenderMenu.add(function(c,b){
							
								c=b.addMenu({title:UPMETmce.LoginRegistrationForms});
									a.addImmediate(c,UPMETmce.FrontRegistrationForm, '[upme_registration]');
									a.addImmediate(c,UPMETmce.RegFormCustomRedirect, '[upme_registration redirect_to="http://url_here"]');
									a.addImmediate(c,UPMETmce.RegFormCaptcha, '[upme_registration captcha=yes]');
									a.addImmediate(c,UPMETmce.RegFormNoCaptcha, '[upme_registration captcha=no]');
									a.addImmediate(c,UPMETmce.FrontLoginForm, '[upme_login]');
									a.addImmediate(c,UPMETmce.SidebarLoginWidget, '[upme_login use_in_sidebar=yes]');
									a.addImmediate(c,UPMETmce.LoginFormCustomRedirect, '[upme_login redirect_to="http://url_here"]');
									a.addImmediate(c,UPMETmce.LogoutButton, '[upme_logout]');
									a.addImmediate(c,UPMETmce.LogoutButtonCustomRedirect, '[upme_logout redirect_to="http://url_here"]');
								
								b.addSeparator();
								
								c=b.addMenu({title:UPMETmce.SingleProfile});
										a.addImmediate(c,UPMETmce.LoggedUserProfile,"[upme]" );
										a.addImmediate(c,UPMETmce.LoggedUserProfileUserID,"[upme show_id=true]" );
										a.addImmediate(c,UPMETmce.PostAuthorProfile,"[upme id=author]" );
										a.addImmediate(c,UPMETmce.SpecificUserProfile,"[upme id=X]" );

										a.addImmediate(c,UPMETmce.LoggedUserProfileHideStats,"[upme show_stats=no]" );
										a.addImmediate(c,UPMETmce.LoggedUserProfileUserRole,"[upme show_role=yes]" );
										a.addImmediate(c,UPMETmce.LoggedUserProfileStatus,"[upme show_profile_status=yes]" );
										a.addImmediate(c,UPMETmce.LoggedUserProfileLogoutRedirect,"[upme logout_redirect=http://url_here]" );

								
								b.addSeparator();
								
								c=b.addMenu({title:UPMETmce.MultipleProfilesMemberList});
									a.addImmediate(c,UPMETmce.GroupSpecificUsers, '[upme group=user_id1,user_id2,user_id3,etc]');
									a.addImmediate(c,UPMETmce.AllUsers, '[upme group=all users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersCompactView, '[upme group=all view=compact users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersCompactViewHalfWidth, '[upme group=all view=compact width=2 users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersModalWindow, '[upme group=all view=compact modal=yes users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersNewWindow, '[upme group=all view=compact new_window=yes users_per_page=10]');
									a.addImmediate(c,UPMETmce.UsersBasedUserRole, '[upme group=all role=subscriber users_per_page=10]');
									a.addImmediate(c,UPMETmce.AdministratorUsersOnly, '[upme group=all role=administrator users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersOrderedDisplayName, '[upme group=all order=asc orderby=display_name users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersOrderedPostCount, '[upme group=all order=desc orderby=post_count users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersOrderedRegistrationDate, '[upme group=all order=desc orderby=registered users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersOrderedCustomField, '[upme group=all order=desc orderby=custom_field_meta_key orderby_custom=yes users_per_page=10]');
									a.addImmediate(c,UPMETmce.AllUsersUserID, '[upme group=all show_id=true users_per_page=10]');
									a.addImmediate(c,UPMETmce.GroupUsersCustomField, '[upme group=all  group_meta=custom_field_key group_meta_value=custom_field_value users_per_page=10]');
									a.addImmediate(c,UPMETmce.HideUsersUntilSearch, '[upme group=all users_per_page=10 hide_until_search=true]');
									a.addImmediate(c,UPMETmce.SearchProfile, '[upme_search operator=OR]');
									a.addImmediate(c,UPMETmce.SearchCustomFieldFilters, '[upme_search filters=meta1,meta2,meta3]');

								
								b.addSeparator();
								
								a.addImmediate(b,UPMETmce.PrivateContentLoginRequired, '[upme_private]Place member only content here[/upme_private]');
								
								b.addSeparator();
								
								c=b.addMenu({title:UPMETmce.ShortcodeOptionExamples});
									a.addImmediate(c,UPMETmce.HideUserStatistics, '[upme show_stats=no]');
									a.addImmediate(c,UPMETmce.HideUserSocialBar, '[upme show_social_bar=no]');
									a.addImmediate(c,UPMETmce.HalfWidthProfileView, '[upme width=2]');
									a.addImmediate(c,UPMETmce.CompactViewNoExtraFields, '[upme view=compact]');
									a.addImmediate(c,UPMETmce.CustomizedProfileFields, '[upme view=meta_id1,meta_id2,meta_id3]');
									a.addImmediate(c,UPMETmce.ShowUserIDProfiles, '[upme show_id=true]');
									a.addImmediate(c,UPMETmce.LimitResultsMemberList, '[upme group=all view=compact limit_results=yes users_per_page=4 ]');
									a.addImmediate(c,UPMETmce.ShowResultCountMemberList, '[upme group=all view=compact users_per_page=10 show_result_count=yes  ]');

							});
						return d
					
					} // End IF Statement
					
					return null
				},
		
				addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand( "mceInsertContent",false,a)}})}
				
			}
		);
		
		tinymce.PluginManager.add( "UPMEShortcodes", tinymce.plugins.UPMEShortcodes);
	}
)();