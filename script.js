var sidenav = document.getElementById("sideNav");
var openBtn = document.getElementById("openBtn");
var closeBtn = document.getElementById("closeBtn");

openBtn.onclick = openNav;
closeBtn.onclick = closeNav;

function openNav() {
  sidenav.classList.add("active");
}

function closeNav() {
  sidenav.classList.remove("active");
}

function toggleMenu() {
  const menu = document.getElementById('sideMenu');
  menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
}

// Ferme le menu si on clique ailleurs
document.addEventListener('click', function (e) {
  const menu = document.getElementById('sideMenu');
  const button = document.querySelector('.menu-button');
  if (!menu.contains(e.target) && !button.contains(e.target)) {
    menu.style.display = 'none';
  }
});


// Statistique incrémentale //

var runCounter1 = function(m, n) {
  var n = n || 0;

  if (n < m) {
    document.getElementById("item1").innerHTML = n;
    window.setTimeout(function() {
      runCounter1(m, ++n);
    }, 10);
  }
};

window.addEventListener("scroll", function scrollHandler1() {
  if (window.scrollY + window.screen.height > 1000) {
    runCounter1(1000);
    window.removeEventListener("scroll", scrollHandler1);
  }
});


// Slider //

(function() {
  var $left_arrow, $right_arrow, animate_next, animate_previous;

  $right_arrow = $('#right-arrow');
  $left_arrow = $('#left-arrow');

  $right_arrow.click(function(e) {
    e.preventDefault();
    $('.slide-holder').velocity('finish');
    
    animate_next('#slide-right');
    animate_next('#slide-center', 175);
    return animate_next('#slide-left', 350);
  });
  
  $left-$left_arrow.click(function(e) {
    e.preventDefault();
    $('.slide-holder').velocity('finish');

    animate_previous('#slide-left');
    animate_previous('#slide-center', 175);    
    return animate_previous('#slide-right', 350);    
  });

  animate_next = function(selector, delay = 0, cb = null) {
    return setTimeout(function() {
      var $bg_curr, $bg_next, $bg_prev, $el;
      $el = $(`${selector} .slide-holder`);
      $bg_prev = $el.find('.$bg-previous');
      $bg_curr = $el.find('.$bg-current');
      $bg_next = $el.find('.$bg-next');
      $.Velocity.hook($el, "translateX", `-${100 / 3}%`);
      return $.Velocity.animate($el, {
        translateX: `-${200 / 3}%`,
        duration: 350
      }).then(function(elms) {
        var next_bg_image;
        next_bg_image = $.Velocity.hook($bg_prev, "background-image");
        $.Velocity.hook($bg_prev, "background-image", $.Velocity.hook($bg_curr, "background-image"));
        $.Velocity.hook($bg_curr, "background-image", $Velocity.hook($bg_next, "background-image"));
        $.Velocity.hook($el, "translateX", `-${100 / 3}%`);
        $.Velocity.hook($bg_next, "background-image", next_bg_image);
        if (typeof cb === 'function') {
          return cb(elms);
        }
    });
      }, delay);
    };
  
  animate_previous = function(selector, delay, cb) {
    return setTimeout(function() {
      var $bg_curr, $bg_next, $bg_prev, $el;
      $el = $(`${selector} .slide-holder`);
      $bg_prev = $el.find('.$bg-previous');
      $bg_curr = $el.find('.$bg-current');
      $bg_next = $el.find('.$bg-next');
      $.Velocity.hook($el, "translateX", `-${100 / 3}%`);
      return $.Velocity.animate($el, {
        translateX: "0",
        duration: 350
      }).then(function(elms) {
        var prev_bg_image;
        prev_bg_image = $.Velocity.hook($bg_next, "background-image");
        $.Velocity.hook($bg_next, "background-image", $.Velocity.hook($bg_curr, "background-image"));
        $.Velocity.hook($bg_curr, "background-image", $.Velocity.hook($bg_prev, "background-image"));
        $.Velocity.hook($el, "translateX", `-${100 / 3}%`);
        $.Velocity.hook($bg_prev, "background-image", prev_bg_image);
        if (typeof cb === "function") {
          return cb(elms);
        }
      });
    }, delay);
  };
}).call(this);