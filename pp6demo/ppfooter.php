		<!-- content column (end) -->
		<!-- right column (begin) -->
		<!-- Note: DO NOT CHANGE THE ID OR CLASS AND DO NOT REMOVE THE COMMENTS! -->
		<div id="ewCart" class="ewClientTemplate col-sm-2 hidden-xs">			
			<div class="affix" data-spy="affix">
				<h4>{{html P.Phrase("ShopCart")}}</h4>	
				{{if items.length}}		
				<form name="simple" class="form-horizontal" role="form">
					<!-- {{each items}} -->
					<div class="form-group"> 
						<div class="col-sm-12">
							${$value.itemname}
						</div>	
						<div class="col-sm-3 control-label">${ $value.price}</div>
						<label for="Qty" class="col-sm-2 control-label">{{html P.Phrase('DescQuantity')}}</label>
								{{if P.UNDEFINED_QUANTITY}}
								<div class="col-sm-7">
									<input type="text" class="form-control ewQty input-sm" name="quantity_${ $value.index}" value="${ $value.quantity}" data-index="${ $value.index}" placeholder="Qty">								
								{{else}}
								<div class="col-sm-7">${$value.quantity}
								{{/if}}
									<button type="button" class="ewRemove btn btn-default btn-sm" data-index="${ $value.index}"><span class="glyphicon glyphicon-remove"></span></button>
								</div>
					</div>
					<!-- {{/each}} -->
					<div class="form-group">
						<div class="col-sm-12">
							<label>{{html P.Phrase('DescTotal')}}</label> 
							<span>${total}</span>
						</div>
					</div>
					<br>
					<button type="button" class="btn btn-primary" name="btnCheckout" id="btnCheckout" value="${ P.Phrase('Checkout')}">${ P.Phrase('Checkout')}</button>
				</form>
				{{else}}
					<div class="alert alert-info">{{html P.Phrase('CartEmptyMessage')}}</div>
				{{/if}}
				</div>	
		</div>
		<!-- Enter your HTML below the tiny shopping cart here -->
		<!-- right column (end) -->
	</div>
</div>
<!-- content (end) -->
<div class="clearfix"></div>
<?php echo ewpp_DebugMsg(); // Debug ?>		
</div>	
<div class="clearfix"></div>
<!-- footer (begin) --><!-- *** Note: Only licensed users are allowed to remove or change the following copyright statement. *** -->
<div class="ewFooterRow" id="ewFooterRow">	
	<div class="ewFooterText">&copy;2014 e.World Technology Ltd. All rights reserved.</div>
	<!-- Place other links, for example, disclaimer, here -->		
</div>
<!-- footer (end) -->
<script type="text/javascript">
</script>	
</div>
</div>
</div>
</body>
</html>
