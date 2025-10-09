<?php
namespace SIM\MAILPOSTING;
use SIM;

const MODULE_VERSION		= '8.0.7';

DEFINE(__NAMESPACE__.'\MODULE_PATH', plugin_dir_path(__DIR__));

//module slug is the same as grandparent folder name
DEFINE(__NAMESPACE__.'\MODULE_SLUG', strtolower(basename(dirname(__DIR__))));

add_filter('sim_submenu_mailposting_options', __NAMESPACE__.'\menuOptions', 10, 2);
function menuOptions($optionsHtml, $settings){
	if(empty($settings['category-mapper'])){
		$categoryMapper	= [''];
	}else{
		$categoryMapper	= $settings['category-mapper'];
	}

	$postTypes	= get_post_types([
		'public'   => true
	]);

	$categories	= [];
	foreach($postTypes as $postType){
		$categories[$postType]	= [];

		foreach(get_object_taxonomies($postType) as $taxonomy){
			$categories[$postType][$taxonomy]	= get_categories( array(
				'orderby' 	=> 'name',
				'order'   	=> 'ASC',
				'taxonomy'	=> $taxonomy,
				'hide_empty'=> false,
			) );
		}
	}

	ob_start();
	
    ?>
	<div class="">
		<h4>Give optional e-mail addresses and the categories their posts should be given:</h4>
		<div class="clone-divs-wrapper">
			<?php
			foreach($categoryMapper as $index=>$mapper){
				if(empty($mapper)){
					$mapper	= [];
				}
				?>
				<div class="clone-div" data-div-id="<?php echo $index;?>" style="display:flex;border: #dedede solid; padding: 10px; margin-bottom: 10px;">
					<div class="multi-input-wrapper">
						<label>
							<h4 style='margin: 0px;'>E-mail address <?php echo $index+1;?></h4>
							<input type='email' name="category-mapper[<?php echo $index;?>][email]" value='<?php echo $mapper['email'];?>'>
						</label>
						<label>
							<h4 style='margin-bottom: 0px;'>The category e-mails from this address should be mapped to</h4>
						</label>

						<?php
							foreach($categories as $postType=>$taxonomies){
								?>
								<div class='posttype-wrapper' data-posttype='<?php echo $postType;?>'>
									<label>
										<input type='checkbox' class='posttype' name='category-mapper[<?php echo $index;?>][category][]' value='<?php echo $postType;?>' <?php if(isset($mapper['category'][$postType])){echo 'checked';}?>>
										<?php echo ucfirst($postType);?>
									</label>

									<div class='taxonomies-wrapper <?php if(!isset($mapper['category'][$postType])){echo 'hidden';}?>' style='margin-left: 20px;'>
										<?php
										foreach($taxonomies as $taxonomy=>$cats){
											?>
											<div class='taxonomy-wrapper'>
												<label>
													<input type='checkbox' class='taxonomy' name='category-mapper[<?php echo $index;?>][category][<?php echo $postType;?>][]' value='<?php echo $taxonomy;?>' <?php if(isset($mapper['category'][$postType][$taxonomy])){echo 'checked';}?>>
													<?php echo ucfirst($taxonomy);?>
												</label>

												<div class='categories-wrapper <?php if(!isset($mapper['category'][$postType][$taxonomy])){echo 'hidden';}?>'  style='margin-left: 40px;'>
													<?php
													foreach($cats as $cat){
														?>
														<label>
															<input type='checkbox' name='category-mapper[<?php echo $index;?>][category][<?php echo $postType;?>][<?php echo $taxonomy;?>][]' value='<?php echo $cat->term_id;?>' <?php if(isset($mapper['category'][$postType][$taxonomy]) && is_array($mapper['category'][$postType][$taxonomy]) && in_array($cat->term_id, $mapper['category'][$postType][$taxonomy])){echo 'checked';}?>>
															<?php echo $cat->name;?>
														</label>
														<?php
													}
													?>
												</div>
											</div>
											<?php
										}
										?>
									</div>
								</div>
								<?php
							}
						?>
					</div>
					<div class='button-wrapper' style='margin:auto;'>
						<button type="button" class="add button" style="flex: 1;">+</button>
						<?php
						if(count($categoryMapper)> 1){
							?>
							<button type="button" class="remove button" style="flex: 1;">-</button>
							<?php
						}
						?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	return $optionsHtml.ob_get_clean();
}

//run on module activation
add_filter('sim_module_mailposting_after_save', __NAMESPACE__.'\onUpdate');
function onUpdate($options){
	SIM\ADMIN\installPlugin('postie/postie.php');

	return $options;
}