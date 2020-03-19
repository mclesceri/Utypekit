@extends('Layouts.metronic')

@section('title', 'Test Page')

@section('content')
<div class="m-grid__item m-grid__item--fluid m-wrapper">
	<!-- BEGIN: Subheader -->
	@include('../Elements/flash-message')
	<!-- END: Subheader -->
	
	<!-- Custome Message Disply -->
		<div class="message-alert"></div>
	<!-- Custome Message Disply -->
	<div class="m-content">
		
		<div class="m-portlet m-portlet--mobile">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">
							ORDERS LIST
						</h3>
					</div>
				</div>			
				<div class="m-portlet__head-tools">
					<ul class="m-portlet__nav">
						<li class="m-portlet__nav-item">
							<a href="{{URL::to('/order')}}" class="btn btn-accent m-btn m-btn--custom m-btn--pill m-btn--icon m-btn--air removeOrderCookiee">
								<span>
									<i class="la la-plus"></i>
									<span>Add New Order</span>
								</span>
							</a>
						</li>
						
					</ul>
				</div>
			</div>
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 order-head">
				<p>
					To begin, choose one of the Current Orders from the list below and click Edit.
				</p>
			</div>		
			<div class="m-portlet__body">
				<!--begin: Datatable -->
				<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_1">
					<thead>
						<tr>
							<th>ID</th>
							<th>Order Title</th>
							<th>Date Added</th>
							<th>Order Number</th>
							<th>Chairperson</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					@if(count($orders) !== 0)
					<tbody>
						@foreach ($orders as $order)
							<tr>
								<td>{{$order->id}}</td>
								<td>{{$order->order_title}}</td>
								<td>{{date('Y-m-d',strtotime($order->created_at))}}</td>
								<td>{{$order->order_number}}</td>
								<td>{{Auth::user()->name}}</td>
								@if ($order->status === 0)
									<td>Inactive</td>
								@elseif($order->status === 1)
									<td>Data Entry</td>
								@else
									<td></td>
								@endif
								<td>
									<a href="{{URL::to('/recipe-list/'.base64_encode(convert_uuencode($order->id)))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="View Recipes">		
										<i class="fa fa-eye"></i>						
									</a>
									<a href="{{ URL::to('order/' . base64_encode(convert_uuencode($order->id))) }}" class="m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill removeOrderCookiee" title="Edit">		
										<i class="la la-edit"></i>
									</a>
									<a href="javascript:void(0);" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" onClick="deleteRecord({{$order->id}}, 'orders', this);">		
										<i class="la la-trash"></i>						
									</a>
								</td>
							</tr>
						@endforeach
					</tbody>
					@else
					<tbody>
						<tr>
							<td colspan="8"><b>There are no Order yet.</b></td>
						</tr>	
					</tbody>
					@endif	
				</table>
			</div>
		</div>
	</div>
</div>
@endsection