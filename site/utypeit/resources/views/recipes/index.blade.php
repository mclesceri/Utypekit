@extends('Layouts.metronic')

@section('title', 'Test Page')


@section('content')


<div class="m-grid__item m-grid__item--fluid m-wrapper">
<!-- BEGIN: Subheader -->
	<div class="m-subheader ">
		<div class="d-flex align-items-center">
			<div class="mr-auto">
				<h3 class="m-subheader__title "> Recipe List for Order: {{$orderInfo->order_number}} </h3>
			</div>
			
			<div class="mr-auto">
				<h3 class="m-subheader__title "> Total Recipes This Order: {{$recipes->total()}} </h3>
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
	
		<div class="m-portlet m-portlet--mobile">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">
							Recipes List
						</h3>
					</div>
				</div>
				<div class="m-portlet__head-tools">
					<ul class="m-portlet__nav">
						<li class="m-portlet__nav-item">
							<a href="{{URL::to('/manage-recipe/'.base64_encode(convert_uuencode($order_id)))}}" class="btn btn-accent m-btn m-btn--custom m-btn--pill m-btn--icon m-btn--air">
								<span>
									<i class="la la-plus"></i>
									<span>Add Recipe</span>
								</span>
							</a>
						</li>
						
					</ul>
				</div>
			</div>
			<div class="m-portlet__body">
				<!--begin: Search Form -->
								<!--<form class="m-form m-form--fit m--margin-bottom-20"> -->
								
								{{ Form::open(array('name'=>"recipe-list", 'class'=>"m-form m-form--fit m--margin-bottom-20", 'id' => "recipe_search", 'method' => 'get')) }}
								
									<div class="row m--margin-bottom-20">
										
										<div class="col-lg-4 m--margin-bottom-10-tablet-and-mobile">
											<label>Search for recipes where the:</label>
											<select  name="recipe_search_for" class="form-control m-input" data-col-index="2">
												<option @if(Request::get('recipe_search_for') == 'id') selected  @endif value="id">ID</option>
												<option @if(Request::get('recipe_search_for') == 'title') selected  @endif value="title">Title</option>
												<option @if(Request::get('recipe_search_for') == 'created_at') selected  @endif value="created_at">date Added</option>
												<option @if(Request::get('recipe_search_for') == 'status') selected  @endif value="status">Status</option>
											</select>
										</div>
										<div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
											<label>Search By: </label>
											<select name="recipe_search_by" class="form-control m-input" data-col-index="2">
												<option @if(Request::get('recipe_search_by') == 'is') selected  @endif value="is">equals</option>
												<option @if(Request::get('recipe_search_by') == 'like') selected  @endif value="like">is like</option>
												<option @if(Request::get('recipe_search_by') == 'not') selected  @endif value="not">is not</option>
											</select>
										</div>
										<div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
											<label>Type Keyword: </label>
											<input name="recipe_search_term" value="{{Request::get('recipe_search_term')}}" type="text" class="form-control m-input" placeholder="Enter value" data-col-index="1">
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<!--<button class="btn btn-brand m-btn m-btn--icon" id="m_search">
												<span>
													<i class="la la-search"></i>
													<span>Search</span>
												</span>
											</button>-->
											
											{{ Form::submit('Search', ['id'=>'', 'class' => "btn btn-brand m-btn m-btn--icon"]) }}
											
											
											&nbsp;&nbsp;
											<!-- <button onClick="window.location.href = recipe-list" class="btn btn-secondary m-btn m-btn--icon" id="m_reset">
												<span>
													<i class="la la-close"></i>
													<span>Reset</span>
												</span>
											</button> -->
											<a href="{{URL::to('/recipe-list/'.base64_encode(convert_uuencode($orderInfo->id)))}}" class="btn btn-secondary m-btn m-btn--icon" title="Edit">		
												<span>
													<i class="la la-close"></i>
													<span>Reset</span>
												</span>
											</a>
										</div>
									</div>
								{{ Form::close() }}
								<div class="m-separator m-separator--md m-separator--dashed"></div>
				<!--begin: Datatable -->
				<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_1">
					<thead>
						<tr>
							<th width="80px">@sortablelink('id')</th>
							<th>@sortablelink('title')</th>
							<th>@sortablelink('created_at')</th>
							<th>Added By ID</th>
							<th>@sortablelink('status')</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@if($recipes->count())
						@foreach ($recipes as $recipe)
							<tr>
							<td>{{$recipe->id}}</td>
							<td>{{$recipe->title}}</td>
							<td>{{date('Y-m-d',strtotime($recipe->created_at))}}</td>
							<td>{{Auth::user()->name}}</td>
							<td>
							@if($recipe->status == 1)
								Inactive
							@elseif($recipe->status == 2)
								Data Entry
							@elseif($recipe->status == 3)
								Editorial
							@elseif($recipe->status == 4)
								Approved
							@else
								None
							@endif
							
							</td>
							<td>
								
							
								<a href="{{URL::to('/manage-recipe/'.base64_encode(convert_uuencode($recipe->order_id)).'/'.base64_encode(convert_uuencode($recipe->id)))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill removeOrderCookiee" title="Edit">		
									<i class="la la-edit"></i>
								</a>
									
								<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" onClick="deleteRecord({{$recipe->id}}, 'recipes', this);">		
									<i class="la la-trash"></i>						
								</a>
									
							</td>
						</tr>
						@endforeach
						@endif
					</tbody>
					
				</table>
				 {!! $recipes->appends(\Request::except('page'))->render() !!}
			</div>
		</div>					
	</div>
</div>
@endsection