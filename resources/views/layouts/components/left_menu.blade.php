<div class="left-menu">
    <div data-spy="affix" data-offset-top="60" data-offset-bottom="200">
        <div class="menu-content" id="contextualNav">
            <div class="menuTitle">
                @lang('profile.quick_menu')
            </div>
            <ul class="nav">
                @foreach(config('profile.sections') as $section => $name)
                    <li class="">
                        <a href="#section_{{$section}}">
                            @lang('profile.sections.'.$section)
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>