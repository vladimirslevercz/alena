<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
	{if count($goodsRecommended)}
	<div class="row">
		<h3>Doporučené</h3>
	</div>
	<div class="row">
		<div n:foreach="$goodsRecommended as $good"
			 class="goods-item col-lg-4 col-md-6 col-sm-6 col-xs-12"
			 style="border: 1px dotted red;{foreach $good->related('photo')->limit(1) as $photo}background-image: url('{$basePath}/content/photo/{$photo}.big.jpg');{/foreach}"
		>
			<span class="">{$good->name}
		</div>
	</div>
	<div class="clearfix"></div>
	{/if}

	{if count($goodsOther)}
	<div class="row">
		<h2>{$filterName}</h2>
	</div>
	<div class="row">
		<div n:foreach="$goodsOther as $good" class="goods-item col-lg-4 col-md-6 col-sm-6 col-xs-12">
			{$good->name}
		</div>
	</div>
	<div class="clearfix"></div>
	{/if}

	<div class="clearfix"></div>
</div>