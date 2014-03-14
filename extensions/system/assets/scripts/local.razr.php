define(['vendor/assets/local/local'], function(local) {

       local.meta.date = {
           'short'       : "@trans('DATE_SHORT')",
           'medium'      : "@trans('DATE_MEDIUM')",
           'long'        : "@trans('DATE_LONG')",
           'full'        : "@trans('DATE_FULL')",
           'shortdays'   : ["@trans('Mon')", "@trans('Tue')", "@trans('Wed')", "@trans('Thu')", "@trans('Fri')", "@trans('Sat')", "@trans('Sun')"],
           'longdays'    : ["@trans('Monday')", "@trans('Tuesday')", "@trans('Wednesday')", "@trans('Thursday')", "@trans('Friday')", "@trans('Saturday')", "@trans('Sunday')"],
           'shortmonths' : ["@trans('Jan')", "@trans('Feb')", "@trans('Mar')", "@trans('Apr')", "@trans('May')", "@trans('Jun')", "@trans('Jul')", "@trans('Aug')", "@trans('Sep')", "@trans('Oct')", "@trans('Nov')", "@trans('Dec')"],
           'longmonths'  : ["@trans('January')", "@trans('February')", "@trans('March')", "@trans('April')", "@trans('May')", "@trans('June')", "@trans('July')", "@trans('August')", "@trans('September')", "@trans('October')", "@trans('November')", "@trans('December')"]
       };

       return local;
});