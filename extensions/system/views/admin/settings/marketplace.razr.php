@style('system', 'system/css/system.css')
@script('marketplace', 'system/js/settings/marketplace.js', 'requirejs')

<div id="js-marketplace" data-api="@api" data-key="@key" data-url="@url.to('@system/package/install')" data-installed="@packages|e">

    <form class="uk-form pk-options uk-clearfix">
        <div class="uk-float-left">

            <input type="text" name="q" placeholder="@trans('Search')">

            <select name="type">
                <option value="">@trans('- Type -')</option>
                <option value="extension">@trans('Extension')</option>
                <option value="theme">@trans('Theme')</option>
            </select>

        </div>
    </form>

    <p class="uk-alert uk-alert-info uk-hidden" data-msg="no-packages">@trans('No packages found.')</p>
    <p class="uk-alert uk-alert-warning uk-hidden" data-msg="no-connection">@trans('Cannot connect to the Marketplace. Please try again later.')</p>

    <div class="js-content"></div>
    <div class="js-details uk-modal"></div>

</div>