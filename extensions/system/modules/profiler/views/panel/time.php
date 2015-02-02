<?php

$colors = [
    'default'                => '#aacd4e',
    'section'                => '#666',
    'event_listener'         => '#3dd',
    'event_listener_loading' => '#add',
    'doctrine'               => '#d3d',
    'views'                  => '#F0DB56',
];

$dumpEvents = function($events) {
    $result = [];
    foreach ($events as $name => $event) {
        if ($name === '__section__') continue;

        $array = [
            'name' => $name,
            'category' => $event->getCategory(),
            'origin' => sprintf('%F', $event->getOrigin()),
            'starttime' => sprintf('%F', $event->getStarttime()),
            'endtime' => sprintf('%F', $event->getEndtime()),
            'duration' => sprintf('%F', $event->getDuration()),
            'memory' => sprintf('%.1F', $event->getMemory() / 1024 / 1024),
            'periods' => []
        ];

        foreach ($event->getPeriods() as $period) {
            $array['periods'][] = [
                'start' => sprintf('%F', $period->getStarttime()),
                'end' => sprintf('%F', $period->getEndtime()),
            ];
        }

        $result[] = $array;
    }
    return $result;
}

?>

<h1>Time</h1>

<table class="pf-table">
    <tr>
        <td>Total time</td>
        <td><?php printf('%.0f', $collector->getDuration()) ?> ms</td>
    </tr>
    <tr>
        <td>Initialization time</td>
        <td><?php printf('%.0f', $collector->getInitTime()) ?> ms</td>
    </tr>
    <tr>
        <td>Threshold</td>
        <td>
            <form class="js-timeline-control" action="" method="get">
                <input type="number" size="3" name="threshold" value="1" min="0"> ms
                <input type="hidden" name="panel" value="time">
            </form>
        </td>
    </tr>
</table>

<?php $events = $collector->getEvents() ?>
<?php if (!isset($events['__section__'])) return ?>

<h2>
    <?php echo $profile->getParent() ? "Request" : "Main Request" ?>
    <small> - <?php echo $events['__section__']->getDuration() ?> ms</small>
</h2>

<?php $max = $events['__section__']->getEndTime() ?>

<p class="pf-timeline-legend">
<?php foreach ($colors as $category => $color) : ?>
    <span data-color="<?php echo $color ?>"><?php echo $category ?></span>
<?php endforeach ?>
</p>

<div>
    <canvas id="timeline_<?php echo $token ?>" class="pf-timeline" width="680" height=""></canvas>
</div>

<script>

(function() {

    /**
     * In-memory key-value cache manager
     */
    var cache = new function() {
        "use strict";
        var dict = {};

        this.get = function(key) {
            return dict.hasOwnProperty(key)
                ? dict[key]
                : null;
            };

        this.set = function(key, value) {
            dict[key] = value;

            return value;
        }
    };

    /**
     * Query an element with a CSS selector.
     *
     * @param  string selector a CSS-selector-compatible query string.
     *
     * @return DOMElement|null
     */
    function query(selector)
    {
        "use strict";
        var key = 'SELECTOR: ' + selector;

        return cache.get(key) || cache.set(key, document.querySelector(selector));
    }

    /**
     * Canvas Manager
     */
    function CanvasManager(requests, maxRequestTime) {
        "use strict";

        var _drawingColors  = <?php echo json_encode($colors) ?>,
            _storagePrefix  = 'timeline/',
            _threshold      = 1,
            _requests       = requests,
            _maxRequestTime = maxRequestTime;

        /**
         * Check whether this event is a child event.
         *
         * @return true if it is.
         */
        function isChildEvent(event)
        {
            return '__section__.child' === event.name;
        }

        /**
         * Check whether this event is categorized in 'section'.
         *
         * @return true if it is.
         */
        function isSectionEvent(event)
        {
            return 'section' === event.category;
        }

        /**
         * Get the width of the container.
         */
        function getContainerWidth()
        {

            return query('[data-panel="time"] > div').clientWidth;
        }

        /**
         * Draw one canvas.
         *
         * @param request   the request object
         * @param max       <subjected for removal>
         * @param threshold the threshold (lower bound) of the length of the timeline (in milliseconds).
         * @param width     the width of the canvas.
         */
        this.drawOne = function(request, max, threshold, width)
        {
            "use strict";
            var text,
                ms,
                xc,
                drawableEvents,
                mainEvents,
                elementId    = 'timeline_' + request.id,
                canvasHeight = 0,
                gapPerEvent  = 38,
                colors = _drawingColors,
                space  = 10.5,
                ratio  = (width - space * 2) / max,
                h = space,
                x = request.left * ratio + space, // position
                canvas = cache.get(elementId) || cache.set(elementId, document.getElementById(elementId)),
                ctx    = canvas.getContext('2d');

            // Filter events whose total time is below the threshold.
            drawableEvents = request.events.filter(function(event) {
                return event.duration >= threshold;
            });

            canvasHeight += gapPerEvent * drawableEvents.length;

            canvas.width  = width;
            canvas.height = canvasHeight;

            ctx.textBaseline = "middle";
            ctx.lineWidth = 0;

            // For each event, draw a line.
            ctx.strokeStyle = "#dfdfdf";

            drawableEvents.forEach(function(event) {
                event.periods.forEach(function(period) {
                    var timelineHeadPosition = x + period.start * ratio;

                    if (isChildEvent(event)) {
                        ctx.fillStyle = colors.child_sections;
                        ctx.fillRect(timelineHeadPosition, 0, (period.end - period.start) * ratio, canvasHeight);
                    } else if (isSectionEvent(event)) {
                        var timelineTailPosition = x + period.end * ratio;

                        ctx.beginPath();
                        ctx.moveTo(timelineHeadPosition, 0);
                        ctx.lineTo(timelineHeadPosition, canvasHeight);
                        ctx.moveTo(timelineTailPosition, 0);
                        ctx.lineTo(timelineTailPosition, canvasHeight);
                        ctx.fill();
                        ctx.closePath();
                        ctx.stroke();
                    }
                });
            });

            // Filter for main events.
            mainEvents = drawableEvents.filter(function(event) {
                return ! isChildEvent(event)
            });

            // For each main event, draw the visual presentation of timelines.
            mainEvents.forEach(function(event) {

                h += 8;

                // For each sub event, ...
                event.periods.forEach(function(period) {
                    // Set the drawing style.
                    ctx.fillStyle   = colors['default'];
                    ctx.strokeStyle = colors['default'];

                    if (colors[event.name]) {
                        ctx.fillStyle   = colors[event.name];
                        ctx.strokeStyle = colors[event.name];
                    } else if (colors[event.category]) {
                        ctx.fillStyle   = colors[event.category];
                        ctx.strokeStyle = colors[event.category];
                    }

                    // Draw the timeline
                    var timelineHeadPosition = x + period.start * ratio;

                    if ( ! isSectionEvent(event)) {
                        ctx.fillRect(timelineHeadPosition, h + 3, 2, 6);
                        ctx.fillRect(timelineHeadPosition, h, (period.end - period.start) * ratio || 2, 6);
                    } else {
                        var timelineTailPosition = x + period.end * ratio;

                        ctx.beginPath();
                        ctx.moveTo(timelineHeadPosition, h);
                        ctx.lineTo(timelineHeadPosition, h + 11);
                        ctx.lineTo(timelineHeadPosition + 8, h);
                        ctx.lineTo(timelineHeadPosition, h);
                        ctx.fill();
                        ctx.closePath();
                        ctx.stroke();

                        ctx.beginPath();
                        ctx.moveTo(timelineTailPosition, h);
                        ctx.lineTo(timelineTailPosition, h + 11);
                        ctx.lineTo(timelineTailPosition - 8, h);
                        ctx.lineTo(timelineTailPosition, h);
                        ctx.fill();
                        ctx.closePath();
                        ctx.stroke();

                        ctx.beginPath();
                        ctx.moveTo(timelineHeadPosition, h);
                        ctx.lineTo(timelineTailPosition, h);
                        ctx.lineTo(timelineTailPosition, h + 2);
                        ctx.lineTo(timelineHeadPosition, h + 2);
                        ctx.lineTo(timelineHeadPosition, h);
                        ctx.fill();
                        ctx.closePath();
                        ctx.stroke();
                    }
                });

                h += 30;

                ctx.beginPath();
                ctx.strokeStyle = "#dfdfdf";
                ctx.moveTo(0, h - 10);
                ctx.lineTo(width, h - 10);
                ctx.closePath();
                ctx.stroke();
            });

            h = space;

            // For each event, draw the label.
            mainEvents.forEach(function(event) {

                ctx.fillStyle = "#444";
                ctx.font = "12px sans-serif";
                text = event.name;
                ms = " ~ " + (event.duration < 1 ? event.duration : parseInt(event.duration, 10)) + " ms / ~ " + event.memory + " MB";
                if (x + event.starttime * ratio + ctx.measureText(text + ms).width > width) {
                    ctx.textAlign = "end";
                    ctx.font = "10px sans-serif";
                    xc = x + event.endtime * ratio - 1;
                    ctx.fillText(ms, xc, h);

                    xc -= ctx.measureText(ms).width;
                    ctx.font = "12px sans-serif";
                    ctx.fillText(text, xc, h);
                } else {
                    ctx.textAlign = "start";
                    ctx.font = "12px sans-serif";
                    xc = x + event.starttime * ratio + 1;
                    ctx.fillText(text, xc, h);

                    xc += ctx.measureText(text).width;
                    ctx.font = "10px sans-serif";
                    ctx.fillText(ms, xc, h);
                }

                h += gapPerEvent;
            });
        };

        this.drawAll = function(width, threshold)
        {
            "use strict";

            width     = width || getContainerWidth();
            threshold = threshold || this.getThreshold();

            var self = this;

            _requests.forEach(function(request) {
                self.drawOne(request, maxRequestTime, threshold, width);
            });
        };

        this.getThreshold = function() {
            var threshold = localStorage[_storagePrefix + 'threshold'];

            if (threshold === null) {
                return _threshold;
            }

            _threshold = parseInt(threshold);

            return _threshold;
        };

        this.setThreshold = function(threshold)
        {
            _threshold = threshold;

            localStorage[_storagePrefix + 'threshold'] = threshold;

            return this;
        };
    };

    function canvasAutoUpdateOnResizeAndSubmit(e) {
        e.preventDefault();
        canvasManager.drawAll();
    }

    function canvasAutoUpdateOnThresholdChange(e) {
        canvasManager
            .setThreshold(query('input[name="threshold"]').value)
            .drawAll();
    }

    var requests_data = {
        "max": <?php echo printf("%F", $events['__section__']->getEndTime()) ?>,
        "requests": [<?php echo json_encode(['id' => $token, 'left' => 0, 'events' => $dumpEvents($events)]) ?>
        ]
    };

    var canvasManager = new CanvasManager(requests_data.requests, requests_data.max);

    query('input[name="threshold"]').value = canvasManager.getThreshold();
    canvasManager.drawAll();

    // Update the colors of legends.
    var timelineLegends = document.querySelectorAll('.pf-timeline-legend > span[data-color]');

    for (var i = 0; i < timelineLegends.length; ++i) {
        var timelineLegend = timelineLegends[i];

        timelineLegend.style.borderLeftColor = timelineLegend.getAttribute('data-color');
    }

    // Bind event handlers
    var elementTimelineControl  = query('.js-timeline-control'),
        elementThresholdControl = query('input[name="threshold"]');

    window.onresize                 = canvasAutoUpdateOnResizeAndSubmit;
    elementTimelineControl.onsubmit = canvasAutoUpdateOnResizeAndSubmit;

    elementThresholdControl.onclick  = canvasAutoUpdateOnThresholdChange;
    elementThresholdControl.onchange = canvasAutoUpdateOnThresholdChange;
    elementThresholdControl.onkeyup  = canvasAutoUpdateOnThresholdChange;

    // TODO: this is only a workaround, to trigger canvas update, once the panel is opened
    [].forEach.call(document.querySelector("#pk-profiler").querySelectorAll('[data-name]'), function(el) {
        el.addEventListener('click', function() {
            setTimeout(function() {
                canvasAutoUpdateOnThresholdChange(null);
            }, 250);
        });
    });

    setTimeout(function() {
        canvasAutoUpdateOnThresholdChange(null);
    }, 50);

})();
</script>