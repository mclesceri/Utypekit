<!DOCTYPE html>

<html>
	<head>
		<title>
			@isset($data['pdf_name'])
				{{$data['pdf_name']}}
			@endisset
		</title>
		<style>
			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
			}
			th, td {
				padding: 5px;
				text-align: left;
			}
		</style>
	</head>
	
	<body>
		@if($data['preview_section'] == 1)  
			<h1> Cover Information </h1>
			<table style="width:100%"> 
				@isset($data['pdf_name'])
					
					<tr>
						<td> PDF Name: </td>
						<td> {{$data['pdf_name']}}  </td>
					</tr>
				@endisset	
				
				@isset($data['pdf_printed_liners'])
					<tr>
						<td> PDF printed liners </td>
						<td> {{$data['pdf_printed_liners']}}  </td>
					</tr>
				@endisset
				
				@isset($data['pdf_paper_stock'])
					<tr>
						<td> PDF Paper Stock: </td>
						<td> {{$data['pdf_paper_stock']}} </td>
					</tr>
				@endisset
			</table>
		@endif
		
		@if($data['preview_section'] == 2)  
			<h1> Divider Information </h1>
			<table style="width:100%"> 
				@isset($data['divider_name'])
					<tr>
						<td> Divider Name </td>
						<td> {{$data['divider_name']}} </td>
					</tr>
				@endisset
				
				@isset($data['divider_paper_stock'])
					<tr>
						<td> Divider paper stock </td>
						<td> {{$data['divider_paper_stock']}} </td>
					</tr>
				@endisset
			</table>
		@endif
		
		@if($data['preview_section'] == 3)
			<h1> Recipe Information </h1>
		
			@isset($data['recipeTitle'])
				@foreach ($data['recipeTitle'] as $recipeKey => $title)
					<table style="width:100%"> 
						<tr>
							<td> Recipe Title </td>
							<td> {{$title}} </td>
						</tr>
						@isset($data['recipes_category'][$recipeKey])
							<tr>
								<td> Recipe Category </td>
								<td> {{$data['recipes_category'][$recipeKey]}} </td>
							</tr>
						@endisset
						@isset($data['recipeTag'][$recipeKey])
							<tr>
								<td> Recipe Description </td>
								<td> {{$data['recipeTag'][$recipeKey]}} </td>
							</tr>
						@endisset
					</table>
				@endforeach
				<br><br>
				<table style="width:100%"> 
					@foreach ($recipes_images as $count => $imageInfo)
					<?php $count++; ?>
						<tr>
							<td> Recipe Image {{$count}} </td>
							<td> <img alt="Image Here" src="{!! asset('/uploads/recipes/draft/'.$imageInfo->imageName) !!}" /> </td>
						</tr>
					@endforeach
				</table>
					
					
			@endisset
			
		@endif
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	</body>
</html>