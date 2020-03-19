@extends('Layouts.metronic')

@section('title', 'Test Page')


@section('content')

<?php 
// echo "<pre>";
// print_r($orderInfo);die; 


// echo $fetchedData->recipesParts->exists();die;

?>

<div class="m-grid__item m-grid__item--fluid m-wrapper">
<!-- BEGIN: Subheader -->
	<div class="m-subheader ">
		<div class="d-flex align-items-center">
			<div class="mr-auto">
				<h3 class="m-subheader__title ">Add recipe</h3>
				
			</div>
			<div>
				<span class="m-subheader__daterange" id="m_dashboard_daterangepicker">
					<span class="m-subheader__daterange-label">
						<span class="m-subheader__daterange-title"></span>
						<span class="m-subheader__daterange-date m--font-brand"></span>
					</span>
					<a href="#" class="btn btn-sm btn-brand m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill">
						<i class="la la-angle-down"></i>
					</a>
				</span>
			</div>
		</div>
	</div>	
	@include('../Elements/flash-message')
<!-- END: Subheader -->

	<div class="m-content">
		<div id="dynamic">
			<div id="boxes">
				<div class="sideBox hidden" id="recipe_icons">
					<div class="sideTab" id="ri_tab">Recipe Icons</div>
					<div class="sideContent" id="ri_content">
					<p>Need Help  <a params="lightwindow_width=500,lightwindow_height=300" class="lightwindow help" title="Recipe Icons" href="http://cookbookpublishers.com/utypeit2/src/data/help/recipe_icons.html">?</a></p>
					
					
					<div data-ot-delay="1" data-ot="none" id="none"><img onclick="setRecipeIcon(this)" alt="none" src="{{ asset('images/recipe_icons/icon_none.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Cancer Ribbon" id="Cancer Ribbon">
						<img onclick="setRecipeIcon(this)" alt="Cancer Ribbon" src="{{asset('images/recipe_icons/icon_cancer_ribbon.png') }}">
					</div>
					
					<div data-ot-delay="1" data-ot="Diabetic" id="Diabetic"><img onclick="setRecipeIcon(this)" alt="Diabetic" src="{{asset('images/recipe_icons/icon_diabetic.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Freezes Well" id="Freezes Well"><img onclick="setRecipeIcon(this)" alt="Freezes Well" src="{{asset('images/recipe_icons/icon_freezes_well.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Gluten-Free" id="Gluten-Free"><img onclick="setRecipeIcon(this)" alt="Gluten-Free" src="{{asset('images/recipe_icons/icon_gluten-free.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Heart Healthy" id="Heart Healthy"><img onclick="setRecipeIcon(this)" alt="Heart Healthy" src="{{asset('images/recipe_icons/icon_heart_healthy.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Heirloom" id="Heirloom"><img onclick="setRecipeIcon(this)" alt="Heirloom" src="{{asset('images/recipe_icons/icon_heirloom.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Hot and Spicy" id="Hot and Spicy"><img onclick="setRecipeIcon(this)" alt="Hot and Spicy" src="{{asset('images/recipe_icons/icon_hot_and_spicy.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="In Memory" id="In Memory"><img onclick="setRecipeIcon(this)" alt="In Memory" src="{{asset('images/recipe_icons/icon_in_memory.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="International" id="International"><img onclick="setRecipeIcon(this)" alt="International" src="{{asset('images/recipe_icons/icon_international.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Kids Recipes" id="Kids Recipes"><img onclick="setRecipeIcon(this)" alt="Kids Recipes" src="{{asset('images/recipe_icons/icon_kids_recipes.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Low Fat" id="Low Fat"><img onclick="setRecipeIcon(this)" alt="Low Fat" src="{{asset('images/recipe_icons/icon_low_fat.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Pets" id="Pets"><img onclick="setRecipeIcon(this)" alt="Pets" src="{{asset('images/recipe_icons/icon_pets.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Quick and Easy" id="Quick and Easy"><img onclick="setRecipeIcon(this)" alt="Quick and Easy" src="{{asset('images/recipe_icons/icon_quick_and_easy.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Slow Cooker" id="Slow Cooker"><img onclick="setRecipeIcon(this)" alt="Slow Cooker" src="{{asset('images/recipe_icons/icon_slow_cooker.png') }}"></div>
					
					<div data-ot-delay="1" data-ot="Vegetarian" id="Vegetarian"><img onclick="setRecipeIcon(this)" alt="Vegetarian" src="{{asset('images/recipe_icons/icon_vegetarian.png') }}"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="m-portlet m-portlet--tabs">
			{{ Form::open(array('name'=>"recipe", 'class'=>"m-form m-form--label-align-right m-form--fit", 'id' => "recipe_form")) }}
				<div class="m-portlet__body">
					<div class="tab-content">
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">Icon: </label>
							<div class="col-lg-6 col-xl-4 icon_image">
							</div>
							
							<label class="col-lg-2 col-form-label">Status:</label>
							<div class="col-lg-6 col-xl-4">
								<select name="status" class="form-control">
									<option @if(isset($fetchedData->status) && @$fetchedData->status == 0) selected  @endif value="0">Select</option>
									<option @if(isset($fetchedData->status) && @$fetchedData->status == 1) selected  @endif value="1">Inactive</option>
									<option @if(isset($fetchedData->status) && @$fetchedData->status == 2) selected  @endif value="2">Data Entry</option>
									<option @if(isset($fetchedData->status) && @$fetchedData->status == 3) selected  @endif value="3">Editorial</option>
									<option @if(isset($fetchedData->status) && @$fetchedData->status == 4) selected  @endif value="4">Approved</option>
								</select>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">Date added:</label>
							<div class="col-lg-6 col-xl-4">
								{{ Form::text('order_added_by', Carbon\Carbon::parse(@$fetchedData->created_at)->toFormattedDateString(), array('class' => 'form-control', 'disabled'=>true)) }}
							</div>
							
							<label class="col-lg-2 col-form-label">Date modified: </label> 
							<div class="col-lg-6 col-xl-4">
								{{ Form::text('order_added_by', Carbon\Carbon::parse(@$fetchedData->updated_at)->toFormattedDateString(), array('class' => 'form-control', 'disabled'=>true)) }}
							</div>
						</div>
						{{ Form::hidden('order_id', @$order_id, array('class' => 'recipe_order_id')) }}
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">Category: </label>
							<div class="col-lg-6 col-xl-4">
								<?php $categortData 	= 	unserialize($orderInfo->category_title); ?>
								<select id="order_category" name="category" class="form-control">
									<option value="-1">Select</option>
									@foreach ($categortData as $key => $category)
										<option @if(isset($fetchedData->category) && @$fetchedData->category == $key) selected  @endif value="{{ $key }}">{{ $category }}</option>
									@endforeach
								</select>
							</div>
							<label class="col-lg-2 col-form-label">Subcategory:  </label> 
							<div class="col-lg-6 col-xl-4">
								<select id="order_subcategory" name="subcategory" class="form-control">
									<option value="0">Select</option>
									
								</select>
							</div>
						</div>
						
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label">Title:</label>
							<div class="col-lg-6 col-xl-4">
								{{ Form::text('title', @$fetchedData->title, array('class' => 'form-control')) }}
							</div>
							
							<label class="col-lg-2 col-form-label">Subtitle: </label> 
							<div class="col-lg-6 col-xl-4">
								{{ Form::text('subtitle', @$fetchedData->subtitle, array('class' => 'form-control')) }}
							</div>
						</div>
						
						<div class="m-separator m-separator--dashed"></div>
						
						<div class="m-portlet__head">
							<div class="m-portlet__head-caption">
								<label class="m-subheader__title">Recipe Contributors </label>&nbsp;
								<a href="javascript:void(0);" class="help-info" data-alias="recipe_contributor">
									<i class="flaticon-questions-circular-button"></i>
								</a>
								
							</div>
							<div class="m-portlet__head-tools">
								<ul class="m-portlet__nav">									
									<li class="m-portlet__nav-item">
										<a onclick="createTableRows('table-draggable-contributor', 'contributor')" href="javascript:void(0)" id="insert-more-category" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air">
											<span>
												<span >Add Contributor</span>
											</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
						
						<table class="table table-striped- table-bordered table-hover table-checkable table-draggable-contributor" id="m_table_contributor">
							<tbody class="connectedSortable">
								<tr>
									<th> First Name </th>
									<th> Last Name </th>
									<th> Contributor Information </th>
									<th> Order </th>
									<th> Action </th>
								</tr>
								@if(@$fetchedData->contributors)  
									@foreach ($fetchedData->contributors as $key => $contributor)
										<tr id="contributor_{{$key}}">
											<td class="category-title"> {{ Form::text('fname[]', @$contributor->fname, array('class' => 'form-control')) }} </td>
											<td class="category-title"> {{ Form::text('lname[]', @$contributor->lname, array('class' => 'form-control')) }} </td>
											<td class="category-title"> {{ Form::text('information[]', @$contributor->information, array('class' => 'form-control')) }} </td>
											<td class="category-title sortHandle">-- </td>
											<td>
												<a class="deleteRecipeImg btn btn-secondary" href="javascript:void(0)">Delete </a>
											</td>
										</tr>
									@endforeach
								@else 
								<tr id="contributor_0">
									<td class="category-title"> {{ Form::text('fname[]', null, array('class' => 'form-control')) }} </td>
									<td class="category-title"> {{ Form::text('lname[]', null, array('class' => 'form-control')) }} </td>
									<td class="category-title"> {{ Form::text('information[]', null, array('class' => 'form-control')) }} </td>
									<td class="category-title sortHandle">-- </td>
									<td>
										<a class="deleteRecipeImg btn btn-secondary" href="javascript:void(0)">Delete </a>
									</td>
								</tr>
								@endif 
							</tbody>
						</table>
						<div class="m-separator m-separator--dashed"></div>
						<div class="m-portlet__head">
							<div class="m-portlet__head-caption">
									<label class="m-subheader__title">Recipe Parts </label>&nbsp;
									<a href="javascript:void(0);" class="help-info" data-alias="recipe_part">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								
							</div>
							<div class="m-portlet__head-tools">
								<ul class="m-portlet__nav">
									<li class="m-portlet__nav-item">
										<a onclick="createRecipePart()" href="javascript:void(0)" id="insert-more-category" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air">
											<span>
												<span >Add Recipe Part</span>
											</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div id="main_part">
						@if(@$fetchedData->recipesParts && count($fetchedData->recipesParts) > 0)
						
							@foreach ($fetchedData->recipesParts as $key => $recipesPart)
								<div id="recipe-part{{ $key }}" class="recipe-part">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												Recipe Part Type:
											</div>
										</div>
										
										<div class="col-lg-2 col-form-label"><label class="m-radio m-radio--state-success"><input class="section_type" type="radio" name="section-type-{{$key}}" value="ingredient" checked="checked"> Ingredients List <span></span> </label></div>
										
										<div class="col-lg-2 col-form-label"><label class="m-radio m-radio--state-success"><input class="section_type" type="radio" name="section-type-{{$key}}" value="method"> Recipe Method <span></span> </label></div>
										
										<div class="col-lg-2 col-form-label">Remove Part 
											<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill remove_recipe_part" title="Delete">		
												<i class="la la-trash"></i>						
											</a> 
										</div>
									</div>
									<div class="form-group m-form__group row">
										<label class="col-lg-3 col-form-label">Add a Title to the Ingredients?</label>
										<div class="col-lg-4 col-xl-2">
											<span class="m-switch m-switch--icon-check">
												<input type="hidden" name="ingredient_title" value="0">
												<label>
													<input class="check-status" type="checkbox" name="ingredient_title" value="0">
													<span></span>
												</label>
											</span>
										</div>
										
										<div class="col-lg-6 col-xl-4">
											{{ Form::text('part_title['.$key.']', $recipesPart->part_title, array('class' => 'form-control')) }}
										</div>
										
									</div>
									<table class="table table-striped- table-bordered table-hover table-checkable table-draggable-recipe-ingredient-part m_table_recipe_ingredient_part" id="m_table_recipe_ingredient_part--" >
										<tbody class="connectedSortable">
										@if(@$recipesPart->recipeIngredients)  
											@foreach ($recipesPart->recipeIngredients as $ingredient_key => $ingredient)
												<tr id="recipe_part_{{$ingredient_key}}">
													<td class="">
														{{ Form::text('recipe_ingredients['.$key.'][]', $ingredient->ingredient, array('class' => 'form-control recipe_ingredients', 'placeholder'=>"")) }}
													</td>
													<td class="ingredient-row">
														<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Remove this row" onclick="createRecipePartTableRows('table-draggable-recipe-ingredient-part', 'recipe_part', this)" >		
															<i class="flaticon-add-circular-button"></i>
														</a>
														<a href="javascript:void(0);" class="deleteRecipeImg m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Remove this row">		
															<i class="la la-trash"></i>						
														</a>
													</td>
													
												</tr>
											@endforeach
										@else
											<tr id="recipe_part_0">
												<td class="">
													{{ Form::text('recipe_ingredients[0][]', '', array('class' => 'form-control recipe_ingredients', 'placeholder'=>"")) }}
												</td>
												<td class="ingredient-row">
													<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Remove this row" onclick="createRecipePartTableRows('table-draggable-recipe-ingredient-part', 'recipe_part', this)" >		
														<i class="flaticon-add-circular-button"></i>
													</a>
													<a href="javascript:void(0);" class="deleteRecipeImg m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Remove this row">		
														<i class="la la-trash"></i>						
													</a>
												</td>
											</tr>
										@endif
										</tbody>
									</table>
									<table class="table table-striped- table-bordered table-hover table-checkable table-draggable-recipe-method-part m_table_recipe_method_part" id="m_table_recipe_method_part--" style="display:none;">
										<tbody class="connectedSortable">
											<tr>
												<td colspan="2">
													<textarea name="recipe_method[{{$key}}]" class="form-control recipe_method" data-provide="markdown" rows="10">@if(isset($fetchedData->part_method)) 
														{{$fetchedData->part_method}}
														@endif
													</textarea>
													<span class="m-form__help">Enter some markdown content</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							@endforeach
						@else
							<div id="recipe-part1" class="recipe-part">
								<div class="m-portlet__head">
									<div class="m-portlet__head-caption">
										<div class="m-portlet__head-title">
											Recipe Part Type:
										</div>
									</div>
									
									<div class="col-lg-2 col-form-label"><label class="m-radio m-radio--state-success"><input class="section_type" type="radio" name="section-type-0" value="ingredient" checked="checked"> Ingredients List <span></span> </label></div>
									
									<div class="col-lg-2 col-form-label"><label class="m-radio m-radio--state-success"><input class="section_type" type="radio" name="section-type-0" value="method"> Recipe Method <span></span> </label></div>
									
									<div class="col-lg-2 col-form-label">Remove Part 
										<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill remove_recipe_part" title="Delete">		
											<i class="la la-trash"></i>						
										</a> 
									</div>
								</div>
								<div class="form-group m-form__group row">
									<label class="col-lg-3 col-form-label">Add a Title to the Ingredients?</label>
									<div class="col-lg-4 col-xl-2">
										<span class="m-switch m-switch--icon-check">
											<input type="hidden" name="ingredient_title" value="0">
											<label>
												<input class="check-status" type="checkbox" name="ingredient_title" value="0">
												<span></span>
											</label>
										</span>
									</div>
									
									<div class="col-lg-6 col-xl-4">
										{{ Form::text('part_title[0]', '', array('class' => 'form-control part_cls')) }}
									</div>
									
								</div>
								<table class="table table-striped- table-bordered table-hover table-checkable table-draggable-recipe-ingredient-part m_table_recipe_ingredient_part" id="m_table_recipe_ingredient_part--" >
									<tbody class="connectedSortable">
										<tr id="recipe_part_0">
											<td class="">
												{{ Form::text('recipe_ingredients[0][]', '', array('class' => 'form-control recipe_ingredients', 'placeholder'=>"")) }}
											</td>
											<td class="ingredient-row">
												<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Remove this row" onclick="createRecipePartTableRows('table-draggable-recipe-ingredient-part', 'recipe_part', this)" >		
													<i class="flaticon-add-circular-button"></i>
												</a>
												<a href="javascript:void(0);" class="deleteRecipeImg m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Remove this row">		
													<i class="la la-trash"></i>						
												</a>
											</td>
											
										</tr>
									</tbody>
								</table>
								<table class="table table-striped- table-bordered table-hover table-checkable table-draggable-recipe-method-part m_table_recipe_method_part" id="m_table_recipe_method_part--" style="display:none;">
									<tbody class="connectedSortable">
										<tr>
											<td colspan="2">
												<textarea name="recipe_method[0]" class="form-control recipe_method" data-provide="markdown" rows="10"></textarea>
												<span class="m-form__help">Enter some markdown content</span>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						@endif
						</div>
						<div class="m-separator m-separator--dashed"></div>
						
						<div class="form-group m-form__group row">
							<label class="col-lg-2 col-form-label"> Add a Note?	 </label>
							<div class="col-lg-6 col-xl-4">
								<span class="m-switch m-switch--icon-check">
									<input type="hidden" name="add_recipe_note" value="0">
									<label>
										<input class="check-status" type="checkbox" name="add_recipe_note" value="0">
										<span></span>
									</label>
								</span>
							</div>
						</div>
						<div class="form-group m-form__group row recipeNoteSection">
							<label class="col-lg-2 col-form-label">Recipe Note: </label>
							<div class="col-lg-10 col-xl-10">
									<textarea name="note_desp" class="form-control" data-provide="markdown" rows="10">@if(isset($fetchedData->note)) 
									{{$fetchedData->note}}
									@endif
								</textarea>								
							</div>
						</div>
					</div>
				</div>
				<div class="m-portlet__foot m-portlet__foot--fit">
					<div class="m-form__actions">
						<div class="row">
							<div class="col-lg-4"></div>
							<div class="col-lg-8 ">
								{{ Form::submit('Submit', ['id'=>'', 'class' => "btn btn-accent"]) }}
							</div>
						</div>
					</div>
				</div>
			{{ Form::close() }}
		</div>					
	</div>
</div>

@endsection

@extends('Elements.info-icon')
@extends('Elements.show-filler')