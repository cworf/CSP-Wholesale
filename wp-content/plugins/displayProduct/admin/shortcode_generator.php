<?php 
if (!defined('ABSPATH'))
    die("Can't load this file directly");
function dp_shortcode_generator_template(){ 
?>
<div id="wpwrap">

<div id="wpbody-content" aria-label="Main content" tabindex="0">
	<form id="displayProduct-form" action="" method="post">
            <div class="dp-container">
                <div class="wrap">
                <div class="dp-title-block">
                    <div class="wrap">
                        <div id="icon-tools"><img src="<?php echo DP_DIR;?>/assets/js/display-icon.png"></div><h2>Display product Options</h2>
                        <h5>
                            <span> &nbsp;| <a href="http://sureshopress.com/display-product-for-woocommerce/document" target="_blank">View Plugin Documentation</a></span>
                            <span>Display Product Version <?php echo DP_VER;?></span>
                        </h5>
                    </div>
                </div>
                <div id="dp-header-block" class="dp-header-block clearfix">
                        <h3 id="displayProduct-step-1" class="displayProduct-headline"><?php echo displayproduct_textdomain('selectliststyle');?> <span class="generatoredit"><?php echo displayproduct_textdomain('Edit');?></span></h3>
                </div>
                
                <div id="dp-shortcode-block" class="dp-shortcode-block clearfix">
                        
                                <ul class="displayProduct-liststyle">
                                        <li><label class="fb" for="displayProduct-fb1">
                                                    <input id="displayProduct-fb1" type="radio" name="fb" value="grid" />
                                                    <img src="<?php echo DP_DIR;?>/assets/images/icon_grid.png">
                                                    <div class="displayProduct-layoutname"><?php echo displayproduct_textdomain('Grid');?></div>
                                            </label></li>
                                        <li><label class="fb" for="displayProduct-fb2">
                                                    <input id="displayProduct-fb2" type="radio" name="fb" value="list" />
                                                    <img src="<?php echo DP_DIR;?>/assets/images/icon_list.png">
                                                    <div class="displayProduct-layoutname"><?php echo displayproduct_textdomain('List');?></div>
                                            </label></li>
                                        <li><label class="fb" for="displayProduct-fb3">
                                                    <input id="displayProduct-fb3" type="radio" name="fb" value="table" />
                                                    <img src="<?php echo DP_DIR;?>assets/images/icon_table.png">
                                                    <div class="displayProduct-layoutname"><?php echo displayproduct_textdomain('Table');?></div>
                                            </label></li>
                                        <li><label class="fb" for="displayProduct-fb4">
                                                    <input id="displayProduct-fb4" type="radio" name="fb" value="box" />
                                                    <img src="<?php echo DP_DIR;?>assets/images/icon_box.png">
                                                    <div class="displayProduct-layoutname"><?php echo displayproduct_textdomain('Box');?></div>
                                            </label></li>
                                        <li><label class="fb" for="displayProduct-fb5">
                                                    <input id="displayProduct-fb5" type="radio" name="fb" value="carousel" />
                                                    <img src="<?php echo DP_DIR;?>assets/images/icon_carousel_box.png">
                                                    <div class="displayProduct-layoutname"><?php echo displayproduct_textdomain('Carousel_Box');?></div>
                                            </label></li>
                                        <li><label class="fb" for="displayProduct-fb6">
                                                    <input id="displayProduct-fb6" type="radio" name="fb" value="carouselGrid" />
                                                    <img src="<?php echo DP_DIR;?>assets/images/icon_carousel_grid.png">
                                                    <div class="displayProduct-layoutname"><?php echo displayproduct_textdomain('Carousel_Grid');?></div>
                                            </label></li>
                                </ul>
                </div>
                
                <div id="dp-header-block" class="dp-header-block displayProduct-step-2 displayProduct-step-2-head clearfix">
                        <h3 id="displayProduct-step-2" class="displayProduct-headline"><?php echo displayproduct_textdomain('DisplayOptions');?> <span class="generatoredit"><?php echo displayproduct_textdomain('Edit');?></span></h3>
                </div>
                
                <div id="dp-shortcode-block" class="displayProduct-step-2 dp-shortcode-block clearfix">
                        <table id="displayProduct-table" class="form-table">
                                <tr>
                                        <th><label for="displayProduct-filter"><?php echo displayproduct_textdomain('Select_product');?></label></th>
                                        <td><label for="dp-product-allproduct"><input id="dp-product-allproduct" name="displayProduct-product_condition" type="radio" value="allproduct" checked> <?php echo displayproduct_textdomain('allproduct');?></label>
                                            <label for="dp-product-filterproduct"><input id="dp-product-filterproduct" name="displayProduct-product_condition" type="radio" value="filterproduct"> <?php echo displayproduct_textdomain('filterproduct');?></label>
                                            <select id="displayProduct-filter" name="displayProduct-filter" multiple size="3">
                                                <option value="featured"><?php echo displayproduct_textdomain('featuredproduct');?></option>
                                                <option value="sales"><?php echo displayproduct_textdomain('saleproduct');?></option>
                                                <option value="instock"><?php echo displayproduct_textdomain('instockproduct');?></option>
                                                <option value="outofstock"><?php echo displayproduct_textdomain('outofstockpproduct');?></option>
                                            </select></td>
                                </tr>
                                <tr>
                                        <th><label for="displayProduct-category"><?php echo displayproduct_textdomain('productcategory');?></label></th>
                                        <td><label for="dp-product-allcatogory"><input id="dp-product-allcatogory" name="displayProduct-cat_condition" type="radio" value="allcatogory" checked> <?php echo displayproduct_textdomain('allcategory');?></label>
                                            <label for="dp-product-customcatogory"><input id="dp-product-customcatogory" name="displayProduct-cat_condition" type="radio" value="customcatogory"> <?php echo displayproduct_textdomain('customcategory');?></label>
                                            <select id="displayProduct-category" name="displayProduct-category" multiple size="3">
                                                <?php 
                                                //Pharse Product Category ID and Product Category Name to shortcode generator.
                                                    $product_cat = '';
                                                    $args = array('hide_empty' => false);
                                                    $terms = get_terms("product_cat", $args);
                                                    $count = count($terms);
                                                    if ($count > 0) {
                                                        foreach ($terms as $term) {
                                                            $product_cat.= '<option value="' . $term->slug . '">' . $term->name . '</option>';
                                                        }
                                                    } else {
                                                        $product_cat.= '<option value="nocat">Please Insert product category or add product to category.</option>';
                                                    }
                                                    echo $product_cat;
                                                ?>
                                            </select></td>
                                </tr>
                                <tr>
                                        <th><label for="displayProduct-sort"><?php echo displayproduct_textdomain('sortbuy');?></label></th>
                                        <td><select id="displayProduct-sort" name="displayProduct-sort">
                                                <option value="default"><?php echo displayproduct_textdomain('Default_sorting');?></option>
                                                <option value="popularity"><?php echo displayproduct_textdomain('Sort_by_popularity');?></option>
                                                <option value="newness"><?php echo displayproduct_textdomain('Sort_by_newness');?></option>
                                                <option value="oldest"><?php echo displayproduct_textdomain('Sort_by_oldest');?></option>
                                                <option value="nameaz"><?php echo displayproduct_textdomain('Sort_by_Product_title_a_to_z');?></option>
                                                <option value="nameza"><?php echo displayproduct_textdomain('Sort_by_Product_title_z_to_a');?></option>
                                                <option value="lowhigh"><?php echo displayproduct_textdomain('Sort_by_Price_low_to_high');?></option>
                                                <option value="highlow"><?php echo displayproduct_textdomain('Sort_by_Price_high_to_low');?></option>
                                                <option value="skulowhigh"><?php echo displayproduct_textdomain('Sort_by_SKU_low_to_high');?></option>
                                                <option value="skuhighlow"><?php echo displayproduct_textdomain('Sort_by_SKU_high_to_low');?></option>
                                                <option value="stocklowhigh"><?php echo displayproduct_textdomain('Sort_by_stock_low_to_high');?></option>
                                                <option value="stockhighlow"><?php echo displayproduct_textdomain('Sort_by_stock_high_to_low');?></option>
                                                <option value="random"><?php echo displayproduct_textdomain('Sort_by_random');?></option>
                                            </select></td>
                                </tr>
                                <tr>
                                        <th><label for="displayProduct-perpage"><?php echo displayproduct_textdomain('Products_displayed_per_page');?></label></th>
                                        <td><input type="number" id="displayProduct-perpage" min="1" name="displayed-perpage" value="20" /></td>
                                </tr>
                                
                        </table>
                </div>
                
                <div id="dp-header-block" class="dp-header-block displayProduct-step-3 displayProduct-step-3-head clearfix">
                    <h3 id="displayProduct-step-3" class="displayProduct-headline"><?php echo displayproduct_textdomain('Color');?> <span class="generatoredit"><?php echo displayproduct_textdomain('Edit');?></span></h3>
                </div>
                
                <div id="dp-shortcode-block" class="displayProduct-step-3 dp-shortcode-block clearfix">
                       <table id="displayProduct-table" class="form-table">
                                <tr class="carousel">
                                        <th><label for="displayProduct-arrowanddot"><?php echo displayproduct_textdomain('Arrow_Dot');?></label></th>
                                        <td><select id="displayProduct-arrowanddot" name="displayProduct-arrowanddot">
                                                <option value="1"><?php echo displayproduct_textdomain('Arrow');?></option>
                                                <option value="2"><?php echo displayproduct_textdomain('Show_pagination_Dot');?></option>
                                                <option value="3"><?php echo displayproduct_textdomain('Arrow_and_Dot');?></option>
                                            </select></td>
                                </tr>
                                <tr class="carousel">
                                        <th><label for="displayProduct-arrowstyle"><?php echo displayproduct_textdomain('Arrow_Style');?></label></th>
                                        <td><select id="displayProduct-arrowstyle" name="displayProduct-arrowstyle">
                                                <option value="1">Style 1</option>
                                                <option value="2">Style 2</option>
                                                <option value="3">Style 3</option>
                                                <option value="4">Style 4</option>
                                            </select></td>
                                </tr>
                                <tr class="carousel">
                                        <th><label for="displayProduct-arrowposition"><?php echo displayproduct_textdomain('Arrow_Position');?></label></th>
                                        <td><select id="displayProduct-arrowposition" name="displayProduct-arrowposition">
                                                <option value="sideMiddle"><?php echo displayproduct_textdomain('Side_Middle');?></option>
                                                <option value="topRight"><?php echo displayproduct_textdomain('Top_Right');?></option>
                                                <option value="topLeft"><?php echo displayproduct_textdomain('Top_Left');?></option>
                                            </select></td>
                                </tr>
                                <tr class="showinall">
                                        <th><label for="displayProduct-columns"><?php echo displayproduct_textdomain('Select_Thumbnail_Hover_Effect');?></label></th>
                                        <td><select id="displayProduct-dpanimatehover" name="displayProduct-dpanimatehover">
                                                <option value="disable">Disable</option>
                                                <?php dp_the_animation_option_init();?>
                                       </select></td>
                                </tr>
                                <tr class="box_carousel">
                                        <th><label for="displayProduct-columns"><?php echo displayproduct_textdomain('Select_Hover_Effec_Product_Name');?></label></th>
                                        <td><select id="displayProduct-dpanimatehover_productname" name="displayProduct-dpanimatehover_productname">
                                                <?php dp_the_animation_option_init();?>
                                       </select></td>
                                </tr>
                                <tr class="box_carousel">
                                        <th><label for="displayProduct-columns"><?php echo displayproduct_textdomain('Select_Hover_Effect_excerpt_and_star');?></label></th>
                                        <td><select id="displayProduct-dpanimatehover_star" name="displayProduct-dpanimatehover_star">
                                                <?php dp_the_animation_option_init();?>
                                       </select></td>
                                </tr>
                                <tr class="box_carousel">
                                        <th><label for="displayProduct-columns"><?php echo displayproduct_textdomain('Select_Hover_Effect_Price');?></label></th>
                                        <td><select id="displayProduct-dpanimatehover_price" name="displayProduct-dpanimatehover_price">
                                                <?php dp_the_animation_option_init();?>
                                       </select></td>
                                </tr>
                                <tr class="showintable tablebackground">
                                        <th><label for="displayProduct-tablebackground"><?php echo displayproduct_textdomain('Table_Background_color');?></label></th>
                                        <td><input id="displayProduct-tablebackground" name="displayProduct-tablebackground" type="text" class="dp_picker_color" value="#ffffff" data-default-color="#ffffff"></td>
                                </tr>
                                <tr class="showintable tableheadbackground">
                                        <th><label for="displayProduct-tableheadbackground"><?php echo displayproduct_textdomain('Table_Head_Background_color');?></label></th>
                                        <td><input id="displayProduct-tableheadbackground" name="displayProduct-tableheadbackground" type="text" class="dp_picker_color" value="#DBDADA" data-default-color="#DBDADA"></td>
                                </tr>
                                <tr class="showintable tableheadtextcolor">
                                        <th><label for="displayProduct-tableheadtextcolor"><?php echo displayproduct_textdomain('Table_Head_Text_color');?></label></th>
                                        <td><input id="displayProduct-tableheadtextcolor" name="displayProduct-tableheadtextcolor" type="text" class="dp_picker_color" value="#ffffff" data-default-color="#ffffff"></td>
                                </tr>
                                <tr class="showintable tablerowhovercolor">
                                        <th><label for="displayProduct-tablerowhovercolor"><?php echo displayproduct_textdomain('Table_Row_hover_color');?></label></th>
                                        <td><input id="displayProduct-tablerowhovercolor" name="displayProduct-tablerowhovercolor" type="text" class="dp_picker_color" value="#fafafa" data-default-color="#fafafa"></td>
                                </tr>
                                <tr class="bordercolor">
                                        <th><label for="displayProduct-bordercolor"><?php echo displayproduct_textdomain('Border_color');?></label></th>
                                        <td><input id="displayProduct-bordercolor" name="displayProduct-bordercolor" type="text" class="dp_picker_color" value="#fc5b5b" data-default-color="#fc5b5b"></td>
                                </tr>
                                <tr class="backgroundcolor">
                                        <th><label for="displayProduct-backgroundcolor"><?php echo displayproduct_textdomain('Background_color');?></label></th>
                                        <td><input id="displayProduct-backgroundcolor" name="displayProduct-backgroundcolor" type="text" class="dp_picker_color" value="#fefefe" data-default-color="#fefefe"></td>
                                </tr>
                                <tr class="productnamecolor">
                                        <th><label for="displayProduct-productnamecolor"><?php echo displayproduct_textdomain('Product_name_color');?></label></th>
                                        <td><input id="displayProduct-productnamecolor" name="displayProduct-productnamecolor" type="text" class="dp_picker_color" value="#444444" data-default-color="#444444"></td>
                                </tr>
                                <tr class="productnamehovercolor">
                                        <th><label for="displayProduct-productnamehovercolor"><?php echo displayproduct_textdomain('Product_nam_hover_color');?></label></th>
                                        <td><input id="displayProduct-productnamehovercolor" name="displayProduct-productnamehovercolor" type="text" class="dp_picker_color" value="#A88F5C" data-default-color="#A88F5C"></td>
                                </tr>
                                <tr class="pricecolor">
                                        <th><label for="displayProduct-pricecolor"><?php echo displayproduct_textdomain('Price_color');?></label></th>
                                        <td><input id="displayProduct-pricecolor" name="displayProduct-pricecolor" type="text" class="dp_picker_color" value="#444444" data-default-color="#444444"></td>
                                </tr>
                                <tr class="textcolor">
                                        <th><label for="displayProduct-textcolor"><?php echo displayproduct_textdomain('Text_color');?></label></th>
                                        <td><input id="displayProduct-textcolor" name="displayProduct-textcolor" type="text" class="dp_picker_color" value="#444444" data-default-color="#444444"></td>
                                </tr>
                                <tr class="linkcolor">
                                        <th><label for="displayProduct-linkcolor"><?php echo displayproduct_textdomain('Link_color');?></label></th>
                                        <td><input id="displayProduct-linkcolor" name="displayProduct-linkcolor" type="text" class="dp_picker_color" value="#fc5b5b" data-default-color="#fc5b5b"></td>
                                </tr>
                                <tr class="linkhovercolor">
                                        <th><label for="displayProduct-linkhovercolor"><?php echo displayproduct_textdomain('Link_hover_color');?></label></th>
                                        <td><input id="displayProduct-linkhovercolor" name="displayProduct-linkhovercolor" type="text" class="dp_picker_color" value="#A88F5C" data-default-color="#A88F5C"></td>
                                </tr>
                                <tr class="buttoncolor">
                                        <th><label for="displayProduct-buttoncolor"><?php echo displayproduct_textdomain('Button_color');?></label></th>
                                        <td><input id="displayProduct-buttoncolor" name="displayProduct-buttoncolor" type="text" class="dp_picker_color" value="#fc5b5b" data-default-color="#fc5b5b"></td>
                                </tr>
                                <tr class="buttonhovercolor">
                                        <th><label for="displayProduct-buttonhovercolor"><?php echo displayproduct_textdomain('Button_hover_color');?></label></th>
                                        <td><input id="displayProduct-buttonhovercolor" name="displayProduct-buttonhovercolor" type="text" class="dp_picker_color" value="#444444" data-default-color="#444444"></td>
                                </tr>
                                <tr class="featuredcolor">
                                        <th><label for="displayProduct-featuredcolor"><?php echo displayproduct_textdomain('featuredcolor');?></label></th>
                                        <td><input id="displayProduct-featuredcolor" name="displayProduct-featuredcolor" type="text" class="dp_picker_color" value="#ffd347" data-default-color="#ffd347"></td>
                                </tr>
                                <tr class="salecolor">
                                        <th><label for="displayProduct-salecolor"><?php echo displayproduct_textdomain('salecolor');?></label></th>
                                        <td><input id="displayProduct-salecolor" name="displayProduct-salecolor" type="text" class="dp_picker_color" value="#fc5b5b" data-default-color="#fc5b5b"></td>
                                </tr>
                                <tr>
                                        <th><label for="displayProduct-font"><?php echo displayproduct_textdomain('Select_Font');?></label></th>
                                        <td><select id="displayProduct-font" name="displayProduct-font">
                                                <option value="Droid+Sans">Droid Sans</option>
                                                <option value="Source+Sans+Pro">Source Sans Pro</option>
                                                <option value="Source+Sans+Pro">Nixie One</option>
                                                <option value="Signika+Negative">Signika Negative</option>
                                                <option value="Lato">Lato</option>
                                                <option value="Lora">Lora</option>
                                                <option value="PT+Sans+Narrow">PT Sans Narrow</option>
                                                <option value="Ubuntu">Ubuntu</option>
                                                <option value="Contrail+One">Contrail One</option>
                                                <option value="Bitter">Bitter</option>
                                                <option value="Lobster">Lobster</option>
                                                <option value="Shadows+Into+Light">Shadows Into Light</option>
                                                <option value="Libre+Baskerville">Libre Baskerville</option>
                                                <option value="Open+Sans">Open Sans</option>
                                                <option value="Open+Sans+Condensed">Open Sans Condensed</option>
                                                <option value="Varela+Round">Varela Round</option>
                                                <option value="Cinzel">Cinzel</option>
                                                <option value="Comfortaa">Comfortaa</option>
                                                <option value="Doppio+One">Doppio+One</option>
                                        </select></td>
                                </tr>
                        </table>
                </div>
		
		</div>
                           
            </div>
            <div class="dp-preview">
                <div id="dp-header-block" class="dp-header-block displayProduct-step-4 displayProduct-step-4-head clearfix">
                    <h3 id="displayProduct-step-4" class="displayProduct-headline"><?php echo displayproduct_textdomain('customize_layout');?> <span class="generatoredit"><?php echo displayproduct_textdomain('Edit');?></span></h3>
                </div>
                
                <div id="dp-shortcode-block" class="displayProduct-step-3 dp-shortcode-block clearfix">
                    <table id="displayProduct-table" class="form-table">
                        <tr>
                            <th><label for="displayProduct-frontsorter"><?php echo displayproduct_textdomain('Frontend_Sorter');?></label></th>
                            <td><select id="displayProduct-frontsorter" name="displayProduct-frontsorter">
                                    <option value="default"><?php echo displayproduct_textdomain('Default');?></option>
                                    <option value="disable"><?php echo displayproduct_textdomain('Disable');?></option>
                            </select></td>
                        </tr>
                        <tr>
                                <th><label for="displayProduct-pagination"><?php echo displayproduct_textdomain('Pagination');?></label></th>
                                <td><select id="displayProduct-pagination" name="displayProduct-pagination">
                                        <option value="default"><?php echo displayproduct_textdomain('Default');?></option>
                                        <option value="disable"><?php echo displayproduct_textdomain('Disable');?></option>
                                    </select></td>
                        </tr>
                        <tr>
                                <th><label for="displayProduct-trimwords"><?php echo displayproduct_textdomain('Trimwords');?></label></th>
                                <td><input type="number" id="displayProduct-trimwords" min="0" name="displayed-trimwords" value="20" /></td>
                        </tr>
                        <tr>
                                <th><label for="displayProduct-quickview"><?php echo displayproduct_textdomain('Quickview');?></label></th>
                                <td><select id="displayProduct-quickview" name="displayProduct-quickview">
                                        <option value="default"><?php echo displayproduct_textdomain('Default');?></option>
                                        <option value="disable"><?php echo displayproduct_textdomain('Disable');?></option>
                                    </select></td>
                        </tr>
                        <tr>
                                <th><label for="displayProduct-addtocartbutton"><?php echo displayproduct_textdomain('Button_and_Quantity');?></label></th>
                                <td><select id="displayProduct-addtocartbutton" name="displayProduct-addtocartbutton">
                                        <option value="default"><?php echo displayproduct_textdomain('Button_default');?></option>
                                        <option value="buttonquantity"><?php echo displayproduct_textdomain('Button_Quantity');?></option>
                                        <option value="productDetail"><?php echo displayproduct_textdomain('Product_detail');?></option>
                                        <option value="customButton"><?php echo displayproduct_textdomain('Custom_Button');?></option>
                                        <option value="customText"><?php echo displayproduct_textdomain('Custom_Text_Call_for_price');?></option>
                                    </select></td>
                        </tr>
                        <tr class="addtocartcustom hideproductDetail">
                                <th><label for="displayProduct-addtocarturl"><?php echo displayproduct_textdomain('Button_Custom_URL');?></label></th>
                                <td><input id="displayProduct-addtocarturl" name="displayProduct-addtocarturl" type="text" placeholder="Leave blank to disable. http://"></td>
                        </tr>
                        <tr class="addtocartcustom productDetail">
                                <th><label for="displayProduct-addtocarttext"><?php echo displayproduct_textdomain('Button_Custom_Text');?></label></th>
                                <td><input id="displayProduct-addtocarttext" name="displayProduct-addtocarttext" type="text" placeholder="Call for price"></td>
                        </tr>
                        <tr class="hideintable">
                                <th><label for="displayProduct-columns"><?php echo displayproduct_textdomain('Columns');?></label></th>
                                <td><input type="number" id="displayProduct-columns" name="displayProduct-columns" min="1" max="6" value="3" /></td>
                        </tr>
                    </table>
                    <div id="sortablegroup">
                    <h4>Disable</h4>
                    <ol id="sortable1" class="simple_with_animation vertical">
                        <li id="displayProduct-title"><div  class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Title');?></div></li>
                        <li id="displayProduct-excerpt"><div  class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Excerpt');?></div></li>
                        <li id="displayProduct-image"><div   class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Image');?></div></li>
                        <li id="displayProduct-price"><div   class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Price');?></div></li>
                        <li id="displayProduct-star"><div   class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Star');?></div>
                        <li id="displayProduct-metagroup"><div  > <?php echo displayproduct_textdomain('Meta_group');?></div></li>
                        <li id="displayProduct-button"><div  > <?php echo displayproduct_textdomain('Button');?></div></li>
                        <li id="displayProduct-featured"><div   class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Featured');?></div></li>
                        <li id="displayProduct-sale"><div   class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Sale');?></div></li>
                        <li id="displayProduct-outofstock"><div  > <?php echo displayproduct_textdomain('Out_of_Stock');?></div></li>
                        <li id="displayProduct-link"><div   class="displayProduct-eneble"> <?php echo displayproduct_textdomain('Link_to_Product_Page');?></div></li>
                    </ol>
                    <h4>Enable</h4>
                    <ol  id="sortable2" class="simple_with_animation vertical">
                    </ol>
                </div>
                
            </div>
            <p class="submit">
                <input type="button" id="displayProduct-submit" class="button-primary" value="<?php echo displayproduct_textdomain('Insert_Product_Shortcode');?>" name="submit" />
            </p>
            <input type="hidden" name="update_settings" value="Y">
            <br><br>
            <hr>
            <h3>Video Tutorial</h3>
        </form>
</div>
    <div class="clear"></div>
    
</div><!-- wpcontent -->


<script type="text/javascript" src="<?php echo admin_url('load-scripts.php');?>?c=1&amp;load%5B%5D=hoverIntent,common,admin-bar,jquery-ui-core,jquery-ui-widget,jquery-ui-mouse,jquery-ui-draggable,jquery-ui-slider,jquery-touch-p&amp;load%5B%5D=unch,iris,wp-color-picker,svg-painter,heartbeat,thickbox"></script>

<script type="text/javascript">
    jQuery(document).ready(function() { 
        var form = jQuery('#displayProduct-form');
        // handles the click event of the submit button
        form.find('#displayProduct-submit').click(function() {
            var options = {
                'fb1': 'grid',
                'fb2': 'list',
                'fb3': 'table',
                'fb4': 'box',
                'fb5': 'carousel',
                'fb6': 'carouselGrid',
                'filter': 'latest',
                'category': 'all',
                'sort': 'default',
                'columns': '3',
                'perpage': '20',
                'pagination': 'default',
                'trimwords': '20',
                'frontsorter': 'default',
                'dpanimatehover': 'disable',
                'dpanimatehover_productname': 'fadeIn',
                'dpanimatehover_star': 'fadeIn',
                'dpanimatehover_price': 'fadeIn',
                'quickview':'default',
                //'skin': 'default',
                'tablebackground': '#ffffff',
                'tableheadbackground': '#DBDADA',
                'tableheadtextcolor':'#ffffff',
                'tablerowhovercolor': '#fafafa',
                'arrowanddot': '1',
                'arrowstyle': '1',
                'arrowposition': 'sideMiddle',
                'backgroundcolor':'#fefefe',
                'bordercolor': '#fc5b5b',
                'productnamecolor': '#444444',
                'productnamehovercolor':'#A88F5C',
                'pricecolor': '#444444',
                'textcolor': '#444444',
                'linkcolor': '#fc5b5b',
                'linkhovercolor':'#A88F5C',
                'buttoncolor': '#fc5b5b',
                'buttonhovercolor':'#444444',
                'featuredcolor': '#ffd347',
                'salecolor':'#fc5b5b',
                'font': 'Droid+Sans',
                'addtocartbutton': 'default',
                'addtocarturl': '',
                'addtocarttext': ''
                
            };
            var layoutsort=jQuery( "#sortablegroup #sortable2 li" );
            var countli=layoutsort.size();
            
            var shortcode = '[displayProduct';
            for (var index in options) {
                var value = jQuery('#displayProduct-' + index).val();
                // Type
                if (index === 'fb1' || index === 'fb2' || index === 'fb3' || index === 'fb4' || index === 'fb5' || index === 'fb6') {
                    if (jQuery('#displayProduct-' + index).is(':checked')) {
                        shortcode += ' type="' + value + '"';
                        dp_type=value;
                    }
//                } else if (index === 'title' || index === 'excerpt' || index === 'image' || index === 'price' || index === 'button'|| index === 'sku'|| index === 'metacategory'|| index === 'metatag' || index === 'featured'|| index === 'outofstock'|| index === 'sale' || index === 'star' || index === 'link'|| index === 'frontsorter') {
//                    if (!jQuery('#displayProduct-' + index).is(':checked')) {
//                        shortcode += ' ' + index + '="hide"';
//                    }
                } else if (index === 'category') {
                    if (value !== null) {
                        shortcode += ' ' + index + '="' + value + '"';
                    }
                } else if (index === 'filter') {
                    if (jQuery('#dp-product-filterproduct').is(':checked')) {
                        shortcode += ' filter="' + value + '"';
                    }
                } else if (index === 'category') {
                    if (jQuery('#dp-product-customcatogory').is(':checked')) {
                        shortcode += ' category="' + value + '"';
                    }
                } else if (value !== options[index]) {
                    shortcode += ' ' + index + '="' + value + '"';
                }
            }
            if(dp_type=='grid'){
                shortcode +=' grid='
                for ( var i = 0; i < countli; i++ ) {
                    shortcode += layoutsort.eq(i).attr('id').replace('displayProduct-','');
                    if(i+1<countli){ shortcode +=','}
                }
            }
            if(dp_type=='list'){
                shortcode +=' list='
                for ( var i = 0; i < countli; i++ ) {
                    shortcode += layoutsort.eq(i).attr('id').replace('displayProduct-','');
                    if(i+1<countli){ shortcode +=','}
                }
            }
            if(dp_type=='box'){
                shortcode +=' box='
                for ( var i = 0; i < countli; i++ ) {
                    shortcode += layoutsort.eq(i).attr('id').replace('displayProduct-','');
                    if(i+1<countli){ shortcode +=','}
                }
            }
            if(dp_type=='table'){
                shortcode +=' table='
                for ( var i = 0; i < countli; i++ ) {
                    shortcode += layoutsort.eq(i).attr('id').replace('displayProduct-','');
                    if(i+1<countli){ shortcode +=','}
                }
            }
            if(dp_type=='carousel'){
                shortcode +=' carousel='
                for ( var i = 0; i < countli; i++ ) {
                    shortcode += layoutsort.eq(i).attr('id').replace('displayProduct-','');
                    if(i+1<countli){ shortcode +=','}
                }
            }
            if(dp_type=='carouselGrid'){
                shortcode +=' carousel_grid='
                for ( var i = 0; i < countli; i++ ) {
                    shortcode += layoutsort.eq(i).attr('id').replace('displayProduct-','');
                    if(i+1<countli){ shortcode +=','}
                }
            }

            shortcode += ']';
            // inserts the shortcode into the active editor
            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

            // closes Thickbox
            tb_remove();
        });
        jQuery('#displayProduct-category,#displayProduct-filter,.table,.carousel,.addtocartcustom').hide();
        jQuery('.displayProduct-step-2').hide();
        jQuery('.displayProduct-step-3').hide();
        jQuery('.displayProduct-step-4').hide();
        resetGridLayout();

        jQuery('.fb').click(function() {
            jQuery('.displayProduct-step-2').fadeIn('fast');
            jQuery('.displayProduct-step-2').next().fadeIn('fast');
            jQuery('.displayProduct-step-3-head').fadeIn('fast');
            jQuery('.displayProduct-step-4-head').fadeIn('fast');

            fbType = jQuery(this).children('input').val();
            if (fbType === 'grid') {
                jQuery('#displayProduct-productnamecolor,#displayProduct-pricecolor,#displayProduct-textcolor').attr({'data-default-color':'#444444','value':'#444444'});
                jQuery('.productnamecolor a.wp-color-result,.pricecolor a.wp-color-result,.textcolor a.wp-color-result').css('background-color','#444444');
                jQuery('#displayProduct-bordercolor').attr({'data-default-color':'#fc5b5b','value':'#fc5b5b'});
                jQuery('.bordercolor a.wp-color-result').css('background-color','#fc5b5b');
                jQuery('#displayProduct-excerpt,#displayProduct-sku,#displayProduct-metacategory,#displayProduct-metatag,#displayProduct-outofstock,#displayProduct-frontsorter').removeAttr('checked');
                jQuery('#displayProduct-frontsorter').attr('checked', 'checked');
                jQuery('.hideintable,.showinall').fadeIn('fast');
                jQuery('.showintable,.box_carousel,.carousel').fadeOut('fast');
//                jQuery('.displayProduct-step-4').fadeIn('fast');
                resetGridLayout();
            } else if (fbType === 'list') {
                jQuery('#displayProduct-productnamecolor,#displayProduct-pricecolor,#displayProduct-textcolor').attr({'data-default-color':'#444444','value':'#444444'});
                jQuery('.productnamecolor a.wp-color-result,.pricecolor a.wp-color-result,.textcolor a.wp-color-result').css('background-color','#444444');
                jQuery('#displayProduct-bordercolor').attr({'data-default-color':'#fc5b5b','value':'#fc5b5b'});
                jQuery('.bordercolor a.wp-color-result').css('background-color','#fc5b5b');
                jQuery('#displayProduct-excerpt,#displayProduct-sku,#displayProduct-metacategory,#displayProduct-metatag,#displayProduct-outofstock,#displayProduct-frontsorter').attr('checked', 'checked');
                jQuery('.hideintable,.showinall').fadeIn('fast');
                jQuery('.showintable,.box_carousel,.carousel').fadeOut('fast');
//                jQuery('.displayProduct-step-4').fadeOut('fast');
                resetListLayout();
            } else if (fbType === 'table') {
                jQuery('#displayProduct-productnamecolor,#displayProduct-pricecolor,#displayProduct-textcolor').attr({'data-default-color':'#444444','value':'#444444'});
                jQuery('.productnamecolor a.wp-color-result,.pricecolor a.wp-color-result,.textcolor a.wp-color-result').css('background-color','#444444');
                jQuery('#displayProduct-bordercolor').attr({'data-default-color':'#eeeeee','value':'#eeeeee'});
                jQuery('.bordercolor a.wp-color-result').css('background-color','#eeeeee');
                jQuery('#displayProduct-sku,#displayProduct-metacategory,#displayProduct-outofstock,#displayProduct-frontsorter').attr('checked', 'checked');
                jQuery('#displayProduct-excerpt,#displayProduct-metatag').removeAttr('checked');
                jQuery('.showintable,.showinall').fadeIn('fast');
                jQuery('.hideintable,.box_carousel,.carousel').fadeOut('fast');
                resetTableLayout();
//                jQuery('.displayProduct-step-4').fadeOut('fast');
            } else if (fbType === 'box') {
                jQuery('#displayProduct-productnamecolor,#displayProduct-pricecolor,#displayProduct-textcolor').attr({'data-default-color':'#ffffff','value':'#ffffff'});
                jQuery('.productnamecolor a.wp-color-result,.pricecolor a.wp-color-result,.textcolor a.wp-color-result').css('background-color','#ffffff');
                jQuery('#displayProduct-bordercolor').attr({'data-default-color':'#fc5b5b','value':'#fc5b5b'});
                jQuery('.bordercolor a.wp-color-result').css('background-color','#fc5b5b');
                jQuery('#displayProduct-frontsorter').attr('checked', 'checked');
                jQuery('#displayProduct-excerpt,#displayProduct-sku,#displayProduct-metacategory,#displayProduct-metatag,#displayProduct-outofstock,#displayProduct-frontsorter').removeAttr('checked');
                jQuery('.hideintable,.showinall,.box_carousel').fadeIn('fast');
                jQuery('.showintable,.carousel').fadeOut('fast');
                resetBoxLayout();
//                jQuery('.displayProduct-step-4').fadeOut('fast');
            } else if (fbType === 'carousel') {
                jQuery('#displayProduct-productnamecolor,#displayProduct-pricecolor,#displayProduct-textcolor').attr({'data-default-color':'#ffffff','value':'#ffffff'});
                jQuery('.productnamecolor a.wp-color-result,.pricecolor a.wp-color-result,.textcolor a.wp-color-result').css('background-color','#ffffff');
                jQuery('#displayProduct-excerpt,#displayProduct-sku,#displayProduct-metacategory,#displayProduct-metatag,#displayProduct-outofstock,#displayProduct-frontsorter').removeAttr('checked');
                jQuery('.hideintable,.showinall,.box_carousel,.carousel').fadeIn('fast');
                jQuery('.showintable').fadeOut('fast');
                resetBoxLayout();
//                jQuery('.displayProduct-step-4').fadeOut('fast');
            } else if (fbType === 'carouselGrid') {
                jQuery('#displayProduct-productnamecolor,#displayProduct-pricecolor,#displayProduct-textcolor').attr({'data-default-color':'#444444','value':'#444444'});
                jQuery('.productnamecolor a.wp-color-result,.pricecolor a.wp-color-result,.textcolor a.wp-color-result').css('background-color','#444444');
                jQuery('#displayProduct-bordercolor').attr({'data-default-color':'#fc5b5b','value':'#fc5b5b'});
                jQuery('.bordercolor a.wp-color-result').css('background-color','#fc5b5b');
                jQuery('#displayProduct-excerpt,#displayProduct-sku,#displayProduct-metacategory,#displayProduct-metatag,#displayProduct-outofstock,#displayProduct-frontsorter').removeAttr('checked');
                jQuery('.hideintable,.showinall,.carousel').fadeIn('fast');
                jQuery('.showintable,.box_carousel').fadeOut('fast');
                resetGridLayout();
//                jQuery('.displayProduct-step-4').fadeOut('fast');
            }
        });
        /* Product Filter  */
        jQuery('#dp-product-allproduct').click(function() {
            jQuery('#displayProduct-filter').fadeOut('fast');
        });
        jQuery('#dp-product-filterproduct').click(function() {
            jQuery('#displayProduct-filter').fadeIn('fast');
        });

        /* Product Category */
        jQuery('#dp-product-allcatogory').click(function() {
            jQuery('#displayProduct-category').fadeOut('fast');
        });
        jQuery('#dp-product-customcatogory').click(function() {
            jQuery('#displayProduct-category').fadeIn('fast');
        });
        
        /* Add to cart button*/
        jQuery('#displayProduct-addtocartbutton').change(function() {
            addtocartval=jQuery(this).val();
            if(addtocartval==='customButton'){
                jQuery('.addtocartcustom').fadeIn('fast');
            }else if(addtocartval==='customText'){
                jQuery('.addtocartcustom').fadeIn('fast');
            }else if(addtocartval==='productDetail'){
                jQuery('.productDetail').fadeIn('fast');
                jQuery('.hideproductDetail').fadeOut('fast');
            }else{
                jQuery('.addtocartcustom').fadeOut('fast');
            }
            
        });
        jQuery('.dp-header-block').click(function(){
            jQuery(this).next().toggle()
        });
        
        jQuery(document).ready(function($) {   
            $('.dp_picker_color').wpColorPicker();
        });
        var adjustment
        jQuery("ol#sortable1,ol#sortable2").sortable({
            group: '.simple_with_animation',
            connectWith: ".simple_with_animation",
            placeholder: "ui-sortable-placeholder",
            pullPlaceholder: false,
            // animation on drop
            onDrop: function  (item, targetContainer, _super) {
              var clonedItem = jQuery('<li/>').css({height: 0})
              item.before(clonedItem)
              clonedItem.animate({'height': item.height()})

              item.animate(clonedItem.position(), function  () {
                clonedItem.detach()
                _super(item)
              })
            },

            // set item relative to cursor position
            onDragStart: function ($item, container, _super) {
              var offset = $item.offset(),
              pointer = container.rootGroup.pointer

              adjustment = {
                left: pointer.left - offset.left,
                top: pointer.top - offset.top
              }

              _super($item, container)
            },
            onDrag: function ($item, position) {
              $item.css({
                left: position.left - adjustment.left,
                top: position.top - adjustment.top
              })
            }
          });
          function resetGridLayout(){
            var SortArrays = [ "#sortable1 li#displayProduct-image","#sortable1 li#displayProduct-title","#sortable1 li#displayProduct-price","#sortable1 li#displayProduct-button" ];
            jQuery.each(SortArrays, function(index, value){
                jQuery(value).appendTo('#sortable2');
            });
          }
          function resetListLayout(){
            var SortArrays = [ "#sortable1 li#displayProduct-image","#sortable1 li#displayProduct-title","#sortable1 li#displayProduct-excerpt","#sortable1 li#displayProduct-price","#sortable1 li#displayProduct-button" ];
            jQuery.each(SortArrays, function(index, value){
                jQuery(value).appendTo('#sortable2');
            });
          }
          function resetBoxLayout(){
            var SortArrays = [ "#sortable1 li#displayProduct-image","#sortable1 li#displayProduct-title","#sortable1 li#displayProduct-excerpt","#sortable1 li#displayProduct-price","#sortable1 li#displayProduct-button" ];
            jQuery.each(SortArrays, function(index, value){
                jQuery(value).appendTo('#sortable2');
            });
          }
          function resetTableLayout(){
            var SortArrays = [ "#sortable1 li#displayProduct-image","#sortable1 li#displayProduct-title","#sortable1 li#displayProduct-excerpt","#sortable1 li#displayProduct-price","#sortable1 li#displayProduct-button" ];
            jQuery.each(SortArrays, function(index, value){
                jQuery(value).appendTo('#sortable2');
            });
          }
    });
    </script>
<div class="clear"></div></div>
<?php } ?>