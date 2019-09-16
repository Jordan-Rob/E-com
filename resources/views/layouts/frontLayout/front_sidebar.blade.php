<div class="left-sidebar">
    <h2>Category</h2>
    <div class="panel-group category-products" id="accordian"><!--category-productsr-->
        <div class="panel panel-default">
            @foreach($categories as $cat)
                @if($cat->status=="1")
                    <div class="panel-heading">
                        <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordian" href="#{{ $cat->id }}">
                                <span class="badge pull-right"><i class="fa fa-plus"></i></span>
                                {{ $cat->name }}
                            </a>
                        </h4>
                    </div>
                    <div id="{{ $cat->id }}" class="panel-collapse collapse">
                        <div class="panel-body">
                            <ul>
                                @foreach($cat->categories as $subcat)
                                    @if($subcat->status=="1")
                                        <li><a href="{{ asset('/products/'.$subcat->url) }}">{{ $subcat->name }} </a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div><!--/category-products-->

    <div class="brands_products"><!--brands_products-->
        <h2>Brands</h2>
        <div class="brands-name">
            <ul class="nav nav-pills nav-stacked">
                <li><a href="#"> <span class="pull-right">(50)</span>Apple</a></li>
                <li><a href="#"> <span class="pull-right">(56)</span>Samsung</a></li>
                <li><a href="#"> <span class="pull-right">(27)</span>Huawei</a></li>
                <li><a href="#"> <span class="pull-right">(32)</span>hp</a></li>
                <li><a href="#"> <span class="pull-right">(5)</span>lenovo</a></li>
                <li><a href="#"> <span class="pull-right">(9)</span>Asus</a></li>
                <li><a href="#"> <span class="pull-right">(4)</span>Dell</a></li>
            </ul>
        </div>
    </div><!--/brands_products-->
    
    <div class="price-range"><!--price-range-->
        <h2>Price Range</h2>
        <div class="well text-center">
             <input type="text" class="span2" value="" data-slider-min="0" data-slider-max="600" data-slider-step="5" data-slider-value="[250,450]" id="sl2" ><br />
             <b class="pull-left">$ 0</b> <b class="pull-right">$ 600</b>
        </div>
    </div><!--/price-range-->
    
    <div class="shipping text-center"><!--shipping-->
        <img src="{{asset('images/frontend_img/home/shipping.jpg')}}" alt="" />
    </div><!--/shipping-->

</div>
        
        
    
        
        
        
        
        
    
    