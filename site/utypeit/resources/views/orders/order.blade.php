@extends('Layouts.metronic')

@section('title', 'Test Page')

@section('content')

<div class="m-grid__item m-grid__item--fluid m-wrapper">
<!-- BEGIN: Subheader -->
	<div class="m-subheader ">
		<div class="d-flex align-items-center">
			<div class="mr-auto">
				<h3 class="m-subheader__title ">Create new order</h3>
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
	
		<div class="m-portlet m-portlet--tabs">
			<div class="m-portlet__head">
				<div class="m-portlet__head-tools">
					<ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--left m-tabs-line--primary main_tab" role="tablist">
						<li class="nav-item m-tabs__item">
							<a class="nav-link m-tabs__link tab_1" data-toggle="tab" href="#m_general_information_page" role="tab" data-tab="tab_1">
								General Information
							</a>
						</li>
						<li class="nav-item m-tabs__item">
							<a class="nav-link m-tabs__link tab_2" data-toggle="tab" href="#m_builder_right_aside" role="tab" data-tab="tab_2">
								 Page Options
							</a>
						</li>
						<li class="nav-item m-tabs__item">
							<a class="nav-link m-tabs__link tab_3" data-toggle="tab"  href="#m_builder_left_aside" role="tab" data-tab="tab_3">
								Recipe Section
							</a>
						</li>
						<li class="nav-item m-tabs__item">
							<a class="nav-link m-tabs__link tab_4" data-toggle="tab"  href="#m_builder_header" role="tab" data-tab="tab_4">
								Design Options
							</a>
						</li>
						
					</ul>
				</div>
			</div>
			
			{{ Form::open(array('url' => 'order', 'name'=>"order", 'class'=>"m-form m-form--label-align-right m-form--fit", 'id' => "order_form", 'enctype' => "multipart/form-data")) }}
				{{ Form::hidden('id',  @$fetchedData->id) }}
				<div class="m-portlet__body">
					<div class="tab-content">
						
						<!--- Tab 1 -->
						<div class="tab-pane" id="m_general_information_page">
							{{ Form::hidden('pdf_name', $pdfName, array('class' => 'form-field')) }}
							{{ Form::hidden('pdf_tag', 'pdf_tag', array('class' => 'form-field')) }}
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label">Order added on </label>
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('created_at', Carbon\Carbon::parse(@$fetchedData->created_at)->toFormattedDateString(), array('class' => 'form-control', 'disabled'=>true)) }}
								</div>
								<label class="col-lg-2 col-form-label">Order modified on </label>
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('updated_at', Carbon\Carbon::parse(@$fetchedData->updated_at)->toFormattedDateString(), array('class' => 'form-control', 'disabled'=>true)) }}
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label">Order added by </label>
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('user_id', @$fetchedData->user->name, array('class' => 'form-control', 'disabled'=>true)) }}
								</div>
								<label class="col-lg-2 col-form-label">Status 
									<a href="javascript:void(0);" class="help-info" data-alias="order_status">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label> 
								<div class="col-lg-6 col-xl-4">
									<select name="status" class="form-control">
										<option @if(@$fetchedData->status == 0) selected  @endif value="0">Choose One...</option>
										<option @if(@$fetchedData->status == 1) selected  @endif value="1">Inactive</option>
										<option @if(@$fetchedData->status == 2) selected  @endif value="2">Data Entry</option>
									</select>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label"><em>*</em>Order Title
									<a href="javascript:void(0);" class="help-info" data-alias="order_title">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('order_title',  @$fetchedData->order_title, array('class' => 'form-control', 'data-valid'=>'required')) }}
								</div>
								<label class="col-lg-2 col-form-label">Order Number
									<a href="javascript:void(0);" class="help-info" data-alias="order_number">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label> 
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('order_number', @$fetchedData->order_number, array('class' => 'form-control', 'disabled'=>true)) }}
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label">Book Title 1
									<a href="javascript:void(0);" class="help-info" data-alias="book_title">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-4 col-xl-4">
									{{ Form::text('book_title1',  @$fetchedData->book_title1, array('class' => 'form-control')) }}
								</div>
								<label class="col-lg-2 col-form-label">Book Title 2 </label>
								<div class="col-lg-4 col-xl-4">
									{{ Form::text('book_title2',  @$fetchedData->book_title2, array('class' => 'form-control')) }}
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label">Book Style
									<a href="javascript:void(0);" class="help-info" data-alias="book_style">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-6 col-xl-4">
									<select name="book_style" class="form-control">
										<option @if(@$fetchedData->book_style == 0) selected  @endif value="0">Choose One...</option>
										<option @if(@$fetchedData->book_style == 1) selected  @endif value="1">Soft Cover</option>
										<option @if(@$fetchedData->book_style == 2) selected  @endif value="2">Hard Cover</option>
										<option @if(@$fetchedData->book_style == 3) selected  @endif value="3">3-Ring Binder</option>
									</select>
								</div>
								<label class="col-lg-2 col-form-label">#Books
									<a href="javascript:void(0);" class="help-info" data-alias="number_of_books">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label> 
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('book_count', @$fetchedData->book_count, array('class' => 'form-control number', 'maxlength'=>3)) }}
								</div>
							</div>
							<div class="m-separator m-separator--dashed"></div>
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label">Organization Type </label>
								<div class="col-lg-6 col-xl-4">
									<select name="organization_type" class="form-control">
										<option value="0">Choose One...</option>
										@foreach ($organization_types as $row)
											<option value="{{ $row->id }}" @if(@$fetchedData->organization_type == $row->id) selected  @endif >{{ $row->organization_name }}</option>
										@endforeach
									</select>
								</div>
								<label class="col-lg-2 col-form-label">Organization Name </label>
								<div class="col-lg-6 col-xl-4">
									{{ Form::text('organization_name', @$fetchedData->organization_name, array('class' => 'form-control')) }}
								</div>
							</div>
							<div class="m-separator m-separator--dashed"></div>
							<div class="form-group m-form__group row">
								<label class="col-lg-2 col-form-label">Option (Printed Liners </label>
								<div class="col-lg-6 col-xl-4">
									<select name="pdf_printed_liners" class="form-control">
										<option value="">Choose One...</option>
										@foreach ($printed_liners as $row)
											<option value="{{ $row->id }}" @if(@$fetchedData->pdf_printed_liners == $row->id) selected  @endif >{{ $row->name }}</option>
										@endforeach
									</select>
								</div>
								<label class="col-lg-2 col-form-label">Option (Paper Stock) </label>
								<div class="col-lg-6 col-xl-4">
									<select name="pdf_paper_stock" class="form-control">
										<option value="">Choose One...</option>
										@foreach ($paper_stock as $row)
											<option value="{{ $row->id }}" @if(@$fetchedData->pdf_paper_stock == $row->id) selected  @endif >{{ $row->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<!--- /Tab 1 -->

						<!--- Tab 2 -->
						<div class="tab-pane " id="m_builder_right_aside">
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">FREE Nutritional Information Pages?</label>
									<a href="javascript:void(0);" class="help-info" data-alias="nutritional_information">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="nutritional_information" value="0" @if(@$fetchedData->nutritional_information == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="nutritional_information" value="1" @if(@$fetchedData->nutritional_information == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Add subcategories to Recipe Sections? </label>
									<a href="javascript:void(0);" class="help-info" data-alias="subcategories_to_recipe">
										<i class="flaticon-questions-circular-button"></i>
									</a>	
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="subcategories_to_recipe" value="0" @if(@$fetchedData->subcategories_to_recipe == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="subcategories_to_recipe" value="1" @if(@$fetchedData->subcategories_to_recipe == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Add a contributor index page to the cookbook? 
									<a href="javascript:void(0);" class="help-info" data-alias="contributors">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="contributors" value="0" @if(@$fetchedData->contributors == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="contributors" value="1" @if(@$fetchedData->contributors == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Recipe index type
									<a href="javascript:void(0);" class="help-info" data-alias="recipe_index_type">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<select name="recipe_index_type" class="form-control">
										<option value="0" @if(@$fetchedData->recipe_index_type == 0) selected  @endif>Choose One...</option>
										<option value="1" @if(@$fetchedData->recipe_index_type == 1) selected  @endif >Alphabetical</option>
										<option value="2" @if(@$fetchedData->recipe_index_type == 2) selected  @endif >As Entered</option>
									</select>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Add an order form to the back of the cookbook?
									<a href="javascript:void(0);" class="help-info" data-alias="order_form_back">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="order_form_back" value="0" @if(@$fetchedData->order_form_back == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="order_form_back" value="1" @if(@$fetchedData->order_form_back == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Wrapper with each individual page</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="wrapper_with_each_page" value="0" @if(@$fetchedData->wrapper_with_each_page == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="wrapper_with_each_page" value="1" @if(@$fetchedData->wrapper_with_each_page == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
						</div>
						<!--- /Tab 2 -->
						
						<!--- Tab 3 -->
						<div class="tab-pane " id="m_builder_left_aside">
							<div class="form-group m-form__group row">
								<div class="col-lg-9 col-xl-9"></div>
								<div class="col-lg-2 col-xl-2">
									<a href="javascript:void(0)" class="btn btn-accent m-btn m-btn--custom m-btn--pill m-btn--icon m-btn--air" id="insert-more-category">
										<span>
											<i class="la la-plus"></i>
											<span>Add Category</span>
										</span>
									</a>
								</div>	
								<table class="table table-striped- table-bordered table-hover table-checkable table-draggable3" id="m_table_2">
									
									<tbody class="connectedSortable">
										@foreach ($recipes_category as $key => $category)
										<tr id="recipe_category_{{$key}}">
											<td class="category-title">
												{{ Form::text('category_title[]', $category->name, array('class' => 'form-control', 'placeholder'=>"")) }}

												<div class="sub-cat-main-div" id="cat_{{$category->id}}" data-count="0">	
													<!--<table class="table table-striped- table-bordered table-hover table-checkable subcattable">
														<tbody class="connectedSortable">
															<tr>
																<td class="col-lg-10 col-xl-10">ABC</td>
																<td class="col-lg-2 col-xl-2">
																	<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill deleteSubCat" title="Delete">	
																		<i class="la la-trash"></i>
																		</a>
																</td>
															</tr>	
														</tbody>
													</table>-->
												</div>		
											</td>
											<td>
												<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill addSubCategory" title="Add Sub Category" data-id-cat="cat_{{$category->id}}">	
													<i class="flaticon-add"></i>				
												</a>
												<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill deleteRecipeImg" title="Delete">	
													<i class="la la-trash"></i>				
												</a>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								<!--<div class="m-portlet m-portlet--mobile">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												
											</div>
										</div>
										<div class="m-portlet__head-tools">
											<ul class="m-portlet__nav">
												<li class="m-portlet__nav-item">
													<a href="javascript:void(0)" id="insert-more" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air">
														<span>
															<span >Add a Recipe</span>
														</span>
													</a>
												</li>
											</ul>
										</div>
									</div>
									<div class="m-portlet__body">
										<table class="table table-striped- table-bordered table-hover table-checkable table-draggable2" id="m_table_1">
											<thead>
												<tr>
													<th>Recipe Title</th>
													<th>Category</th>
													<th>Recipe Description</th>
													<th>Image</th>
													<th>Action</th>
													
												</tr>
											</thead>
											<tbody class="connectedSortable">
												<tr id="recipe_1">
													<td>
														{{ Form::text('recipeTitle[]', null, array('class' => 'form-control', 'placeholder'=>"Recipe Title")) }}
													</td>
													<td>
														<select name="recipes_category[]" class="form-control">
															<option value="">Select</option>
															@foreach ($recipes_category as $row)
																<option value="{{ $row->name }}">{{ $row->name }}</option>
															@endforeach
														</select>
													</td>
													<td>
														{{ Form::textarea('recipeTag[]', null, array('class' => 'form-control', 'placeholder'=>"Recipe description", 'rows'=>"4")) }}
													</td>
													<td>
														<input type="file" accept="image/*" name="image_upload_file[]" id="image_upload_file" class="image_upload_file_new" />
													</td>
													<td>
														<a class="deleteRecipeImg btn btn-secondary" href="javascript:void(0)">Delete </a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>-->
							</div>
							
							<!--<div class="form-group m-form__group row"> 
								<div class="col-lg-10 col-xl-10"></div>
								<div class="col-lg-2 col-xl-2">
									<a class="preview_submit btn btn-primary recipe_section" href="javascript:void(0)">View this Section </a>
								</div>
							</div>-->
						</div>
						<!--- /Tab 3 -->
						
						<!--- Tab 4 -->
						<div class="tab-pane " id="m_builder_header">
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Order Recipes 
									<a href="javascript:void(0);" class="help-info" data-alias="order_recipes_by">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<select name="order_recipes_by" class="form-control">
										<option value="0" @if(@$fetchedData->order_recipes_by == 0) selected  @endif>Choose One...</option>
										<option value="1" @if(@$fetchedData->order_recipes_by == 1) selected  @endif>by Alphabet</option>
										<option value="2" @if(@$fetchedData->order_recipes_by == 2) selected  @endif>Custom Order</option>
									</select>
								</div>
							</div>
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Recipes Continued 
									<a href="javascript:void(0);" class="help-info" data-alias="recipes_continued">
										<i class="flaticon-questions-circular-button"></i>
									</a>	
								</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="recipes_continued" value="0" @if(@$fetchedData->recipes_continued == 0) checked  @endif>
										<label>
											<input id="recipes_continued" class="check-status" type="checkbox" name="recipes_continued" value="1" @if(@$fetchedData->recipes_continued == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Allow Notes
									<a href="javascript:void(0);" class="help-info" data-alias="allow_notes">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="allow_notes" value="0" @if(@$fetchedData->allow_notes == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="allow_notes" value="1" @if(@$fetchedData->allow_notes == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Page Fillers
									<a href="javascript:void(0);" class="help-info" data-alias="use_fillers">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="use_fillers" value="0" @if(@$fetchedData->use_fillers == 0) checked  @endif>
										<label>
											<input class="check-status" id="use_fillers" type="checkbox" name="use_fillers" value="1" @if(@$fetchedData->use_fillers == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Filler Type	 </label>
								<div class="col-lg-8 col-xl-4">
									<select name="filler_type" id="filler_type" class="form-control" disabled="true">
										<option value="0" @if(@$fetchedData->filler_type == 0) selected  @endif>Choose One...</option>
										<option value="1" @if(@$fetchedData->filler_type == 1) selected  @endif>Text Fillers</option>
										<option value="2" @if(@$fetchedData->filler_type == 2) selected  @endif>Image Fillers</option>
									</select>
								</div>
								<div class="col-lg-2 col-xl-2">
									<a class="btn btn-success" href="javascript:void(0)" data-toggle="modal" data-target="#m_modal_show_filler_set">Show Filler Sets</a>
								</div>
							</div>
							
							{{ Form::hidden('filler_set_hidden', @$fetchedData->filler_set, array('id'=>'hidden_filler_set')) }}
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Filler Set</label>
								<div class="col-lg-8 col-xl-4">
									<select disabled="true" name="filler_set" id="filler_set" class="form-control">
										<option value="0">Choose One...</option>
									</select>
								</div>
							</div>
							
							<div class="form-group m-form__group row">
								<label class="col-lg-4 col-form-label">Use Icons
									<a href="javascript:void(0);" class="help-info" data-alias="use_icons">
										<i class="flaticon-questions-circular-button"></i>
									</a>
								</label>
								<div class="col-lg-8 col-xl-4">
									<span class="m-switch m-switch--icon-check">
										<input type="hidden" name="use_icons" value="0" @if(@$fetchedData->use_icons == 0) checked  @endif>
										<label>
											<input class="check-status" type="checkbox" name="use_icons" value="1" @if(@$fetchedData->use_icons == 1) checked  @endif>
											<span></span>
										</label>
									</span>
								</div>
							</div>
							<!--<div class="form-group m-form__group row">
								<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="width: 50%;
								background-color: gray;margin:0 0 0 209px;">
								  <ol class="carousel-indicators">
									<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
									<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
									<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
								  </ol>
								  <div class="carousel-inner" role="listbox">
									<div class="carousel-item active">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/BlackTie_L2.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/Casual_L1_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/Centsaver_L2_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/Classic_L2_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{asset('images/formats/EZRead_L2_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/Fanciful_L2_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/Premiere_L1_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/Traditional_L2_tn.jpg') }}">
									</div>
									<div class="carousel-item">
									  <img class="d-block img-fluid" src="{{ asset('images/formats/WelcomeHome_L1_tn.jpg') }}">
									</div>
								  </div>
								  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									<span class="sr-only">Previous</span>
								  </a>
								  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only">Next</span>
								  </a>
								</div>
							</div>-->
							<!--<div class="form-group m-form__group row"> 
								<div class="col-lg-10 col-xl-10"></div>
								<div class="col-lg-2 col-xl-2">
									<a class="preview_submit btn btn-primary divider_section" href="javascript:void(0)">View this Section </a>
								</div>
							</div>-->
						</div>
						<!--- Tab 4 -->
		
					</div>
				</div>		
				<div class="m-portlet__foot m-portlet__foot--fit">
					<div class="m-form__actions">
						<div class="row">
							<div class="col-lg-4">
								
								{{ Form::hidden('is_preview', 0, array('id' => 'form_preview')) }}
	
								{{ Form::button('Save', ['class' => "btn btn-accent",  'onClick'=>'customValidate("order")']) }}
							</div>
							<div class="col-lg-8 progress-bar" style="background-color:currentColor!important;">	
								<div class="progress">
									<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 5%">
									</div>
								</div>
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