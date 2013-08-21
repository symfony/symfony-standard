var viewport, output, delay, renderers = [];

// checks version
if (phantom.version.major == 1 && phantom.version.minor < 3) {
    console.log('This script requires PhantomJS v1.3.0+, you have v'+phantom.version.major+'.'+phantom.version.minor+'.'+phantom.version.patch);
    phantom.exit(-1);
}

// check usage
if (phantom.args.length !== 2) {
    console.log('Usage: phantom-pdf.js URL dirname');
    phantom.exit(-1);
}

// settings
delay = 500;
viewport = { width: 1024, height: 768 };
output = phantom.args[1];

(function init() {
    var i, slides, workers, slidesPerWorker, page;

    page = new WebPage()
    page.open(phantom.args[0], function (status) {
        if (status !== 'success') {
            console.log('Unable to load the given URL');
            phantom.exit(-1);
        } else {
            slides = page.evaluate(function () {
                return $('.slideContent').length;
            });

            workers = Math.min(4, slides);
            slidesPerWorker = Math.ceil(slides / workers);
            i = 0;

            console.log('Initializing renderers');

            while (slides > 0) {
                if (i > 0) {
                    page = new WebPage();
                }
                page.viewportSize = { width: viewport.width, height: viewport.height };
                page.paperSize = { width: viewport.width * 1.5, height: viewport.height * 1.5 + 30 };
                renderers.push(renderer(page, phantom.args[0], i * slidesPerWorker, Math.min(slidesPerWorker, slides)))
                i++;
                slides -= slidesPerWorker;
            }
        }
    });
}());

function jobFinished(renderer) {
    renderers.splice(renderers.indexOf(renderer), 1);
    if (renderers.length == 0) {
        console.log('Done.');
        phantom.exit(0);
    }
}

function renderer(page, url, currentSlide, slides) {
    page.open(url, openHandler);

    function openHandler(status) {
        if (status !== 'success') {
            console.log('Unable to load the given URL');
            phantom.exit(-1);
        } else {
            // wait to be sure the page is loaded correctly
            setTimeout(initPage, 1000);
        }
    }

    function initPage() {
        var i;
        page.evaluate(function () {
            $('.incremental').css('opacity', '1').removeClass('incremental');
        });
        // move to the current slide
        page.evaluate('function () {'+
            '$(document).slippy().showSlide('+currentSlide+');'+
        '}');
        // wait to be sure the slide animation is over
        setTimeout(renderNextSlide, delay);
    };

    function renderNextSlide() {
        if (!slides) {
            return jobFinished(this);
        }

        console.log('Rendering slide '+currentSlide);
        page.render(output+'slide'+"000".substring(currentSlide.toString().length)+currentSlide+'.pdf');
        page.evaluate(function () {
            $(document).slippy().nextSlide();
        });
        slides--;
        currentSlide++;
        // wait to be sure the slide animation is over
        setTimeout(renderNextSlide, delay);
    }
}
