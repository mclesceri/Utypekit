<div id="categories" number="[[number]]" parent="[[parent]]" order="[[order]]" class="orderListSection">
	<input id="category" type="text" name="category-title_[[number]]-[[parent]]-[[order]]" value="[[name]]" tabindex="[[tabindex]]">
	<div class="categoryControls">[[controls]]</div>
	<button type="button" class="orderListButton" onclick="addSubcategory(this.next('div',0).id,[[number]]); [[number]]">Add Subcategory</button>
	<div id="childlist" class="childList"></div>
</div>