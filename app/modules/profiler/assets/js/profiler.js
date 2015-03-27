(function(xhr) {

    var container = document.getElementById("profiler"),
        url = container.getAttribute("data-url");

        getScript(url+"/vendor/highlight/highlight.js");
        getCss(url+"/vendor/highlight/highlight.css");
        getCss(url+"/css/profiler.css");


    xhr.onload = function() {

       container.innerHTML = this.responseText;

       var scripts = container.getElementsByTagName("script");

       setTimeout(function(){

           for (var i=0; i<scripts.length; i++) {
               eval(scripts[i].innerHTML);
           }

           initToolbar();
           container.style.display = "block";
       }, 500);
    };

    xhr.open("GET", container.getAttribute("data-route"), true);
    xhr.send();

    function initToolbar() {

        var profiler = document.getElementById("pk-profiler"),
            panels   = profiler.querySelectorAll("[data-panel]"),
            active   = false;

        profiler.querySelector(".pf-close").onclick = function() {
            if(active) {
                active.style.display = "";

                if(active.classList) profiler.querySelector('[data-name="'+active.getAttribute('data-panel')+'"]').classList.remove("pf-active");

                active = false;
            }
        };

        for (var i=0;i<panels.length;i++) {
            (function(index) {

                var panel   = panels[index],
                    name    = panel.getAttribute('data-panel'),
                    trigger = profiler.querySelector('[data-name="'+name+'"]');

                if (!trigger) return;

                trigger.style.cursor = "pointer";

                trigger.onclick = function() {
                    if(active) {
                       active.style.display = "";
                       if(trigger.classList) profiler.querySelector('[data-name="'+active.getAttribute('data-panel')+'"]').classList.remove("pf-active");
                    }

                    if(trigger.classList) trigger.classList.add("pf-active");

                    panel.style.display = "block";
                    panel.style.height  = Math.ceil(window.innerHeight/2)+"px";
                    active = panel;
                };
            })(i);
        }
    }

    function getScript(url) {

        var script       = document.createElement('script');
            script.async = true;
            script.src   = url;

        document.getElementsByTagName('head')[0].appendChild(script);
    }

    function getCss(url) {

        var link      = document.createElement('link');
            link.rel  = 'stylesheet';
            link.href = url;

        document.getElementsByTagName('head')[0].appendChild(link);
    }

})(new XMLHttpRequest());