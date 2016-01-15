<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 goods-items">
	<h2>{$filterName}</h2>
	<div class="row">
		{if count($goodsRecommended)}
			<a n:foreach="$goodsRecommended as $good" n:href=":Front:Goods:show $good->id">
				<div
					 class="goods-item recomended-item col-lg-4 col-md-6 col-sm-6 col-xs-12"
					 style="{foreach $good->related('photo')->limit(1) as $photo}background-image: url('{$basePath}/content/photo/{$photo}.big.jpg');{/foreach}"
				>
					<div class="goods-name">{$good->name}</div>
				</div>
			</a>
		{/if}

		{if count($goodsOther)}
			<a n:foreach="$goodsOther as $good" n:href=":Front:Goods:show $good->id">
				<div
					class="goods-item col-lg-4 col-md-6 col-sm-6 col-xs-12"
					style="{foreach $good->related('photo')->limit(1) as $photo}background-image: url('{$basePath}/content/photo/{$photo}.big.jpg');{/foreach}"
					>
					<div class="goods-name">{$good->name}</div>
				</div>
			</a>
		{/if}
	</div>

	<div class="clearfix"></div>
</div>