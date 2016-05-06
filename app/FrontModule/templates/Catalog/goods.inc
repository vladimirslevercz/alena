<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 goods-items" xmlns:n="http://www.w3.org/1999/xhtml">
	<a name="goods"></a>
	<p n:if="$filterName == 'Doporučené'" class="alert alert-warning" style="margin-top: 10px">Katalog se připravuje.</p>
	<p n:if="$filterName != 'Doporučené'" class="pull-right legend">
		<b>Legenda</b>
		doporučujeme <i class="glyphicon glyphicon-thumbs-up" title="Doporučujeme"></i>&nbsp;&nbsp;
		skladem <i><img src="{$basePath}/vectors/stock-icon.svg" style="height: 15px;" title="Skladem"></i>&nbsp;&nbsp;
		na objednávku <i><img src="{$basePath}/vectors/order-icon.svg" style="height: 15px;" title="Na objednávku"></i>&nbsp;&nbsp;
	</p>
	<h2>{$filterName}</h2>
	<div class="row">
		{if count($goodsRecommended)}
			<a n:foreach="$goodsRecommended as $good" n:href=":Front:Goods:show $good->id" class="goods-item-parent">
				<div class="recommended-item col-lg-4 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
					<div
						 class="goods-item"
						 style="{foreach $good->related('photo')->limit(1) as $photo}background-image: url('{$basePath}/content/photo/{$photo}.big.jpg');{/foreach}"
					>
						<div class="flags">
							<span n:if="$good->new" class="flag new-item">NOVINKA</span><br />
							<span n:if="$good->clearance_sale" class="flag clearance-sale-item">DOPRODEJ</span>
						</div>
					</div>
					<div class="goods-name">{$good->name}</div>
					<div class="goods-info">
						<i class="glyphicon glyphicon-thumbs-up" title="Doporučujeme"></i>
						{if $good->stock > 0}
							<i><img src="{$basePath}/vectors/stock-icon.svg" style="height: 20px;" title="Skladem"></i>
						{elseif $good->stock == 0 && $good->order}
							<i><img src="{$basePath}/vectors/order-icon.svg" style="height: 20px;" title="Na objednávku"></i>
						{/if}
					</div>
				</div>
			</a>
		{/if}

		{if count($goodsOther)}
			<a n:foreach="$goodsOther as $good" n:href=":Front:Goods:show $good->id" class="goods-item-parent">
				<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
					<div
						class="goods-item"
						style="{foreach $good->related('photo')->limit(1) as $photo}background-image: url('{$basePath}/content/photo/{$photo}.small.jpg');{/foreach}"
						>
						<div class="flags">
							<span n:if="$good->new" class="flag new-item">NOVINKA</span><br />
							<span n:if="$good->clearance_sale" class="flag clearance-sale-item">DOPRODEJ</span>
						</div>
					</div>
					<div class="goods-name">{$good->name}</div>
					<div class="goods-info">
						{if $good->stock > 0}
							<i><img src="{$basePath}/vectors/stock-icon.svg" style="height: 20px;" title="Skladem"></i>
						{elseif $good->stock == 0 && $good->order}
							<i><img src="{$basePath}/vectors/order-icon.svg" style="height: 20px;" title="Na objednávku"></i>
						{/if}
					</div>
				</div>
			</a>
		{/if}
	</div>

	<div class="clearfix"></div>
</div>