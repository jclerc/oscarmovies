

var Carousel = function( $target )
{
    // Set DOM
    this.$                      = {};
    this.$.history              = $target;
    this.$.pagination_container = this.$.history.find( 'ul' );
    this.$.pagination_items     = this.$.pagination_container.find( 'li' );
    //this.$.siblings_container   = this.$.container.find( '.siblings' );
    //this.$.siblings_items       = this.$.siblings_container.find( '.sibling' );
    //this.$.siblings_next        = this.$.siblings_items.filter( '.next' );
    //this.$.siblings_prev        = this.$.siblings_items.filter( '.prev' );
    this.$.slides_container     = this.$.history.find( '.wrap ul' );
    this.$.slides_items         = this.$.slides_container.find( 'li' );

    // Set properties
    this.count = this.$.slides_items.length;

    // Init
    this.init_events();
};

// Properties
Carousel.prototype.index = 0;
Carousel.prototype.count = 0;
Carousel.prototype.width = 200;

// Methods
Carousel.prototype.init_events = function()
{
    var that = this;

    /*
    // Next click
    this.$.siblings_next.on( 'click', function()
    {
        that.next();

        return false;
    } );

    // Prev click
    this.$.siblings_prev.on( 'click', function()
    {
        that.prev();

        return false;
    } );

    */

    // Pagination click
    this.$.pagination_items.on( 'click', function()
    {
        var $pagination = $( this ),
            index       = $pagination.index();

        that.go_to( index );

        return false;
    } );
};

Carousel.prototype.go_to = function( index )
{
    // Limits
    if( index > this.count - 1 )
        index = 0;
    else if( index < 0 )
        index = this.count - 1;

    // Same
    if( index === this.index )
        return;

    // Animate
    this.$.slides_container.animate( {
        left : - index * this.width
    } );

    // Save new index
    this.index = index;

    // Update pagination
    this.update_pagination();
};

Carousel.prototype.next = function()
{
    this.go_to( this.index + 1 );
};

Carousel.prototype.prev = function()
{
    this.go_to( this.index - 1 );
};

Carousel.prototype.update_pagination = function()
{
    this.$.pagination_items.removeClass( 'active' );
    this.$.pagination_items.eq( this.index ).addClass( 'active' );
};

var $carousels = $( '.carousel' );

$carousels.each( function()
{
    var $carousel = $( this ),
        carousel  = new Carousel( $carousel );
} );

/*
;(function($){
    "use strict";

    var bindToClass      = 'carousel',
        containerWidth   = 0,
        scrollWidth      = 0,
        posFromLeft      = 0,    // Stripe position from the left of the screen
        stripePos        = 0,    // When relative mouse position inside the thumbs stripe
        animated         = null,
        $indicator, $carousel, el, $el, ratio, scrollPos, nextMore, prevMore, pos, padding;

    // calculate the thumbs container width
    function calc(e){
        $el = $(this).find(' > .wrap');
        el  = $el[0];
        $carousel = $el.parent();
        $indicator = $el.prev('.indicator');

        nextMore = prevMore  = false; // reset

        containerWidth       = el.clientWidth;
        scrollWidth          = el.scrollWidth; // the "<ul>"" width
        padding              = 0.2 * containerWidth; // padding in percentage of the area which the mouse movement affects
        posFromLeft          = $el.offset().left;
        stripePos            = e.pageX - padding - posFromLeft;
        pos                  = stripePos / (containerWidth - padding*2);
        scrollPos            = (scrollWidth - containerWidth ) * pos;
        
        if( scrollPos < 0 )
          scrollPos = 0;
        if( scrollPos > (scrollWidth - containerWidth) )
          scrollPos = scrollWidth - containerWidth;
      
        $el.animate({scrollLeft:scrollPos}, 200, 'swing');
        
        if( $indicator.length )
            $indicator.css({
                width: (containerWidth / scrollWidth) * 100 + '%',
                left: (scrollPos / scrollWidth ) * 100 + '%'
            });

        clearTimeout(animated);
        animated = setTimeout(function(){
            animated = null;
        }, 200);

        return this;
    }

    // move the stripe left or right according to mouse position
    function move(e){
        // don't move anything until inital movement on 'mouseenter' has finished
        if( animated ) return;

        ratio     = scrollWidth / containerWidth;
        stripePos = e.pageX - padding - posFromLeft; // the mouse X position, "normalized" to the carousel position

        if( stripePos < 0)
            stripePos = 0;

        pos = stripePos / (containerWidth - padding*2); // calculated position between 0 to 1
        // calculate the percentage of the mouse position within the carousel
        scrollPos = (scrollWidth - containerWidth ) * pos;   

        el.scrollLeft = scrollPos;
        if( $indicator[0] && scrollPos < (scrollWidth - containerWidth) )
          $indicator[0].style.left = (scrollPos / scrollWidth ) * 100 + '%';

        // check if element has reached an edge
        prevMore = el.scrollLeft > 0;
        nextMore = el.scrollLeft < (scrollWidth - containerWidth);

        $carousel.toggleClass('left', prevMore);
        $carousel.toggleClass('right', nextMore);
    }

    $.fn.carousel = function(options){
        $(document)
            .on('mouseenter.carousel', '.' + bindToClass, calc)
            .on('mousemove.carousel', '.' + bindToClass, move);
    };

    // automatic binding to all elements which have the class that is assigned to "bindToClass"
    $.fn.carousel();

})(jQuery);


*/
