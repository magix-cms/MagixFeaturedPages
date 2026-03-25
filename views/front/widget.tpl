<div class="magix-featured-pages-widget py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">{#featured_pages_title#|default:'Pages à la une'}</h2>
        {include file="pages/loop/pages-grid.tpl" data=$featured_pages classType="normal"}
    </div>
</div>