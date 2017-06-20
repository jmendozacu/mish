var vesCategorySelector = new Class.create();
vesCategorySelector.prototype = {
	initialize : function() {
		this.isUsedAjax		= QAQ_USE_AJAX;
		this.categoryIds 	= $('ves_category_ids');
		this.searchBox 		= $('ves_searchbox');
		this.inputSearch 	= this.searchBox.select("input[id='ves_keyword']").first();
		this.noResult 		= true;
		this.searchResult 	= new Array();
		this.searchTimer	= null;
		
		this.attributeSetId = $('attribute_set_id');
		this.container 		= $('catsContent');
		this.rootSelectBox 	= $('select_cat_root');
		this.level 			= this.rootSelectBox.readAttribute('level');
		this.categories 	= _categories;
		this.pathElement 	= $('catePathText');
		this.successIcon 	= $$('.category-success-icon').first();
		this.isEnd 			= false;

		this.initSearch();
		this.getSelectBoxes().each(function(e){
			this.initParams(e);
		}.bind(this));
	},

	initSearch: function() {
		Event.observe(this.inputSearch, 'keyup', this.searchCat.bind(this));
		//Event.observe(this.inputSearch, 'change', this.searchCat.bind(this));
		//Event.observe(this.buttonSearch, 'click', this.searchCat.bind(this));
	},

	searchCat:function() {
		var _this = this;
		clearTimeout(this.searchTimer);
		this.searchTimer = setTimeout(
			function(){
				var keyword = _this.inputSearch.value.toLowerCase();
				_this.searchResult.clear();
				_this.noResult = true;

				var trace = new Array();
				_this.searchCatRecursive(_this.categories,keyword,trace);
				_this.updateSearchResult();
			},
			200
		);
	},

	searchCatRecursive: function(categories, keyword, trace) {
		var _this = this;
		categories.each(function(cat){
			if(cat.label.toLowerCase().indexOf(keyword) >= 0){
				/*This category matchs with the keyword*/
				_this.processSearchResult(cat,trace);
				_this.noResult = false;
			}else{
				/*This category does not match with the keyword*/
				if(cat.data.size()){
					var tmpTrace = trace.clone();
					tmpTrace.push(cat);
					_this.searchCatRecursive(cat.data,keyword,tmpTrace);
				}
			}
		});
	},
	
	processSearchResult:function(category,trace){
		var _this = this;
		var result = trace.clone();
		result.push(category);
		
		if(category.data.size()){
			category.data.each(function(subCategory){
				_this.processSearchResult(subCategory,result);
			});
		}else{
			_this.searchResult.push(result);
		}
	},
	updateSearchResult: function(){
		/*reset the search result*/
		$('searchResult').update('');
		/*Fill search result*/
		if(!this.noResult){
			var html = '<ul class="searchResultUl">';
			this.searchResult.each(function(result){
				var label 	= new Array();
				var values 	= new Array();
				var setId	= null;
				result.each(function(category){
					label.push(category.label);
					values.push(category.value);
					setId = category.attribute_id;
				});
				
				label = label.join(' &gt;&gt; ');
				html += '<li attribute_id="'+setId+'" value="'+values.join(',')+'" class="searchResultLi">'+label+'</li>';
			});
			html += '</ul>';
			$('searchResult').update(html).show();
			this.updateSearchResultEvents();
			$('catsContent').hide();
			$('return-select-content').show();
		}else{
			$('searchResult').update('<strong>No matching results.</strong> You can:<ul style="list-style: decimal inside;"><li><a href="javascript: void(0);" onclick="$(\'ves_keyword\').clear()">Try using another keyword.</a></li><li><a href="javascript: void(0);" onclick="vesSelect.viewCategoresList();">Browse categories and select from our list</a></li></ul>').show();
			$('catsContent').hide();
			$('return-select-content').show();
		}
	},
	updateSearchResultEvents: function(){
		$$('.searchResultLi').each(function(e){
			Event.observe(e,'click',function(){
				$$('.searchResultLi').each(function(x){x.removeClassName('active');});
				e.addClassName('active');
				$('attribute_set_id').setValue(e.readAttribute('attribute_id'));
				$('ves_category_ids').setValue(e.readAttribute('value'));

				$('catePathText').update(e.innerHTML);
				$$('.category-success-icon').first().show();
			});
		});
	},        
	viewCategoresList: function(){
		this.searchResult.clear();
		this.noResult = true;
		$('searchResult').update('').hide();
		$('catsContent').show();
		$('return-select-content').hide();
		$('ves_keyword').value = '';
		this.updatePath(); 
		this.updateCategoryIds();
		this.updateAttribute();
	},
	getSelectBoxes: function() {
		return $$('.ves_catsSelect');
	},

	initParams : function(element) {
		Event.observe(element, 'change', this.selectOnChange.bind(this, element));
	},

	getCurrentLevel: function() {
		return this.level;
	},

	selectOnChange:function(e) {
		var labelPath = $$('.category-path-warp').first().select(".category-selected-label").first();
		if(labelPath.hasClassName('hidden')) {labelPath.removeClassName('hidden');labelPath.show()}

		this.isEnd 			= false;
		var value 			= e.options[e.selectedIndex].value;
		var level 			= e.readAttribute('level');
		var attributeSetId 	= e.options[e.selectedIndex].readAttribute('attribute_id');
		var hasChildrenCategories = e.options[e.selectedIndex].readAttribute('has_children') == "1";
		this.attributeSetId.value = attributeSetId;

		if(level<= this.getCurrentLevel()) {
			this.clearSelectBox(level);
		}
		this.level = level;
		
		this.categoryIds.setValue(this.getCategoryIds());
		
		/*If there is no children categories*/
		if(!hasChildrenCategories) {
			this.isEnd = true;
			this.updatePath();
			return;
		}else{this.updatePath();}
		
		
		if(this.isUsedAjax){
			var _this = this;
			new Ajax.Request(LOAD_CHILDREN_URL, {
				method:'get',
				parameters:{cat_id:value},
				onSuccess: function(transport) {
					var response = transport.responseText;
			    	if(response.isJSON()){
			    		data = response.evalJSON();
			    		console.log(data);
			    		var select = _this.initSelectBox({'level': parseInt(level)+1, 'info': data});
			    		_this.initParams(select);
			    		_this.append(select);
			    	}else{
			    		alert(response);
			    	}
				},
				onFailure: function() { alert('Something went wrong...'); }
			});
		}else{
			var categoryIds = this.getCategoryIds().split(',');
			var leafCategory = this.getLeafCategory(categoryIds);
			var data = {'level': parseInt(level)+1, 'info': leafCategory.data};
			var select = this.initSelectBox(data);
			this.initParams(select);
			this.append(select);
		}
	},

	getLeafCategory: function(categoryIds,categories){
		if(typeof(categories) =='undefined') categories = this.categories;
		var leafCategory = null;
		categories.each(function(category){
			var index = categoryIds.indexOf(category.value);
			if(index !== -1){
				leafCategory = category;
				categoryIds.splice(index,1);
			}
		});
		
		if(categoryIds.size() && leafCategory.data.size()){
			leafCategory = this.getLeafCategory(categoryIds,leafCategory.data);
		}
		
		return leafCategory;
	},
	getCategoryIds: function() {
		var categoryIds = [];
		this.getSelectBoxes().each(function(e){
			if(e.options[e.selectedIndex] !== undefined) {
				categoryIds.push(e.options[e.selectedIndex].value);
			}
		});

		return categoryIds.join();
	},
	updateCategoryIds: function() {
		this.categoryIds.setValue(this.getCategoryIds());
	},

	getAttributeSetId:function() {
		//get last option selected
		return this.getSelectBoxes().last().value?this.getSelectBoxes().last().options[this.getSelectBoxes().last().selectedIndex].readAttribute('attribute_id'):'';
	},

	updateAttribute:function() {
		this.attributeSetId.setValue(this.getAttributeSetId());
	},

	clearPath:function() {
		this.pathElement.update('');
	},

	updatePath:function() {
		this.clearPath();
		var string = '';
		this.getSelectBoxes().each(function(e){
			if(e.options[e.selectedIndex] !== undefined) {
				var label = e.options[e.selectedIndex].text;
				if(string == '') string += label;
				else {
					string += SEPARATOR_SYM+label;
				}
			}
		});
		this.pathElement.innerHTML = string;
		if(this.isEnd) this.successIcon.setStyle({display:'inline-block'});
		else this.successIcon.hide();
	},

	clearSelectBox: function(level) {
		this.getSelectBoxes().each(function(e) {
			if(e.readAttribute('level') > level) e.remove();
		});
	},

	append: function(selectbox) {
		this.container.appendChild(selectbox);
	},

	initSelectBox: function(data) {
		var selectBox = new Element('select', {'class': 'ves_catsSelect', 'size': '10' , 'level': data.level});
		data.info.each(function(x){
			var option = document.createElement('option');
			option.setAttribute('value', x.value);
			option.setAttribute('attribute_id', x.attribute_id);
			option.setAttribute('has_children', x.has_children?"1":"0");
			option.setAttribute('level' , x.level);
			option.text = x.label;

			selectBox.add(option);
		});

		return selectBox;
	}

};

function vesContinue(continueUrl){
	if(!vesSelect.isEnd && vesSelect.noResult) {
		alert('Please choose category.');return
	}else {
		var template = new Template(continueUrl, productTemplateSyntax);
		var attribute_set = $('attribute_set_id').value;
		var type = $('product_type').value;
		var ids = $('ves_category_ids').value;
		setLocation(template.evaluate({attribute_set:attribute_set,type:type,ids:ids}));
	}
}