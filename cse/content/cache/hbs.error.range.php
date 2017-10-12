<?php  function lcr57ec36eba4ccdhbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
  $isBlock = (is_object($cb) && ($cb instanceof \Closure));

  if (isset($cx['blparam'][0][$ch])) {
   return $cx['blparam'][0][$ch];
  }

  $args = $vars[0];
  $options = array(
   'name' => $ch,
   'hash' => $vars[1],
   'contexts' => count($cx['scopes']) ? $cx['scopes'] : array(null),
   'fn.blockParams' => 0,
  );

  if ($isBlock) {
   $options['_this'] = &$op;
  } else {
   $options['_this'] = &$inverted;
  }

  if (isset($vars[2])) {
   $options['fn.blockParams'] = count($vars[2]);
  }

  // $invert the logic
  if ($inverted) {
   $tmp = $else;
   $else = $cb;
   $cb = $tmp;
  }

  if ($isBlock) {
   $options['fn'] = function ($context = '_NO_INPUT_HERE_', $data = null) use ($cx, &$op, $cb, $options, $vars) {
    if ($cx['flags']['echo']) {
     ob_start();
    }
    if (isset($data['data'])) {
     $old_spvar = $cx['sp_vars'];
     $cx['sp_vars'] = array_merge(array('root' => $old_spvar['root']), $data['data'], array('_parent' => $old_spvar));
    }
    $ex = false;
    if (isset($data['blockParams']) && isset($vars[2])) {
     $ex = array_combine($vars[2], array_slice($data['blockParams'], 0, count($vars[2])));
     array_unshift($cx['blparam'], $ex);
    } else if (isset($cx['blparam'][0])) {
     $ex = $cx['blparam'][0];
    }
    if (($context === '_NO_INPUT_HERE_') || ($context === $op)) {
     $ret = $cb($cx, is_array($ex) ? lcr57ec36eba4ccdm($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57ec36eba4ccdm($cx, $context, $ex) : $context);
     array_pop($cx['scopes']);
    }
    if (isset($data['data'])) {
     $cx['sp_vars'] = $old_spvar;
    }
    return $cx['flags']['echo'] ? ob_get_clean() : $ret;
   };
  }

  if ($else) {
   $options['inverse'] = function ($context = '_NO_INPUT_HERE_') use ($cx, $op, $else) {
    if ($cx['flags']['echo']) {
     ob_start();
    }
    if ($context === '_NO_INPUT_HERE_') {
     $ret = $else($cx, $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $else($cx, $context);
     array_pop($cx['scopes']);
    }
    return $cx['flags']['echo'] ? ob_get_clean() : $ret;
   };
  } else {
   $options['inverse'] = function () {
    return '';
   };
  }

  if ($cx['flags']['spvar']) {
   $options['data'] = $cx['sp_vars'];
  }

  $args[] = $options;
  $e = null;
  $r = true;

  try {
   $r = call_user_func_array($cx['helpers'][$ch], $args);
  } catch (\Exception $E) {
   $e = "Runtime: call custom helper '$ch' error: " . $E->getMessage();
  }

  if($e !== null) {
   lcr57ec36eba4ccderr($cx, $e);
  }

  return $r;
 }
 function lcr57ec36eba4ccdencq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57ec36eba4ccdraw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57ec36eba4ccdv($cx, $in, $base, $path, $args = null) {
  $count = count($cx['scopes']);
  while ($base) {
   $v = $base;
   foreach ($path as $name) {
    if (is_array($v) && isset($v[$name])) {
     $v = $v[$name];
     continue;
    }
    if (is_object($v)) {
     if ($cx['flags']['prop'] && !($v instanceof \Closure) && isset($v->$name)) {
      $v = $v->$name;
      continue;
     }
     if ($cx['flags']['method'] && is_callable(array($v, $name))) {
      $v = $v->$name();
      continue;
     }
    }
    if ($cx['flags']['mustlok']) {
     unset($v);
     break;
    }
    return null;
   }
   if (isset($v)) {
    if ($v instanceof \Closure) {
     if ($cx['flags']['mustlam'] || $cx['flags']['lambda']) {
      if (!$cx['flags']['knohlp'] && ($args || ($args === 0))) {
       $A = $args ? $args[0] : array();
       $A[] = array('hash' => $args[1], '_this' => $in);
      } else {
       $A = array($in);
      }
      $v = call_user_func_array($v, $A);
     }
    }
    return $v;
   }
   $count--;
   switch ($count) {
    case -1:
     $base = $cx['sp_vars']['root'];
     break;
    case -2:
     return null;
    default:
     $base = $cx['scopes'][$count];
   }
  }
  if ($args) {
   lcr57ec36eba4ccderr($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57ec36eba4ccdsec($cx, $v, $bp, $in, $each, $cb, $else = null) {
  $push = ($in !== $v) || $each;

  $isAry = is_array($v) || ($v instanceof \ArrayObject);
  $isTrav = $v instanceof \Traversable;
  $loop = $each;
  $keys = null;
  $last = null;
  $isObj = false;

  if ($isAry && $else !== null && count($v) === 0) {
   $ret = $else($cx, $in);
   return $ret;
  }

  // #var, detect input type is object or not
  if (!$loop && $isAry) {
   $keys = array_keys($v);
   $loop = (count(array_diff_key($v, array_keys($keys))) == 0);
   $isObj = !$loop;
  }

  if (($loop && $isAry) || $isTrav) {
   if ($each && !$isTrav) {
    // Detect input type is object or not when never done once
    if ($keys == null) {
     $keys = array_keys($v);
     $isObj = (count(array_diff_key($v, array_keys($keys))) > 0);
    }
   }
   $ret = array();
   if ($push) {
    $cx['scopes'][] = $in;
   }
   $i = 0;
   if ($cx['flags']['spvar']) {
    $old_spvar = $cx['sp_vars'];
    $cx['sp_vars'] = array_merge(array('root' => $old_spvar['root']), $old_spvar, array('_parent' => $old_spvar));
    if (!$isTrav) {
     $last = count($keys) - 1;
    }
   }

   $isSparceArray = $isObj && (count(array_filter(array_keys($v), 'is_string')) == 0);
   foreach ($v as $index => $raw) {
    if ($cx['flags']['spvar']) {
     $cx['sp_vars']['first'] = ($i === 0);
     $cx['sp_vars']['last'] = ($i == $last);
     $cx['sp_vars']['key'] = $index;
     $cx['sp_vars']['index'] = $isSparceArray ? $index : $i;
     $i++;
    }
    if (isset($bp[0])) {
     $raw = lcr57ec36eba4ccdm($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr57ec36eba4ccdm($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
    }
    $ret[] = $cb($cx, $raw);
   }
   if ($cx['flags']['spvar']) {
    if ($isObj) {
     unset($cx['sp_vars']['key']);
    } else {
     unset($cx['sp_vars']['last']);
    }
    unset($cx['sp_vars']['index']);
    unset($cx['sp_vars']['first']);
    $cx['sp_vars'] = $old_spvar;
   }
   if ($push) {
    array_pop($cx['scopes']);
   }
   return join('', $ret);
  }
  if ($each) {
   if ($else !== null) {
    $ret = $else($cx, $v);
    return $ret;
   }
   return '';
  }
  if ($isAry) {
   if ($push) {
    $cx['scopes'][] = $in;
   }
   $ret = $cb($cx, $v);
   if ($push) {
    array_pop($cx['scopes']);
   }
   return $ret;
  }

  if ($v === true) {
   return $cb($cx, $in);
  }

  if (($v !== null) && ($v !== false)) {
   return $cb($cx, $v);
  }

  if ($else !== null) {
   $ret = $else($cx, $in);
   return $ret;
  }

  return '';
 }
 function lcr57ec36eba4ccdifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57ec36eba4ccdp($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57ec36eba4ccderr($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57ec36eba4ccdm($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57ec36eba4ccdm($cx, $a, $b) {
  if (is_array($b)) {
   if ($a === null) {
    return $b;
   } else if (is_array($a)) {
    return array_merge($a, $b);
   } else if (($cx['flags']['method'] || $cx['flags']['prop']) && is_object($a)) {
    foreach ($b as $i => $v) {
     $a->$i = $v;
    }
   }
  }
  return $a;
 }
 function lcr57ec36eba4ccderr($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57ec36eba4ccdraw($cx, $v) {
  if ($v === true) {
   if ($cx['flags']['jstrue']) {
    return 'true';
   }
  }

  if (($v === false)) {
   if ($cx['flags']['jstrue']) {
    return 'false';
   }
  }

  if (is_array($v)) {
   if ($cx['flags']['jsobj']) {
    if (count(array_diff_key($v, array_keys(array_keys($v)))) > 0) {
     return '[object Object]';
    } else {
     $ret = array();
     foreach ($v as $k => $vv) {
      $ret[] = lcr57ec36eba4ccdraw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
if (!class_exists("LS")) {
class LS {
    public function __construct($str, $escape = false) {
        $this->string = $escape ? (($escape === 'encq') ? static::encq(static::$jsContext, $str) : static::enc(static::$jsContext, $str)) : $str;
    }
    public function __toString() {
        return $this->string;
    }
    public static function escapeTemplate($template) {
        return addcslashes(addcslashes($template, '\\'), "'");
    }
    public static function raw($cx, $v) {
        if ($v === true) {
            if ($cx['flags']['jstrue']) {
                return 'true';
            }
        }

        if (($v === false)) {
            if ($cx['flags']['jstrue']) {
                return 'false';
            }
        }

        if (is_array($v)) {
            if ($cx['flags']['jsobj']) {
                if (count(array_diff_key($v, array_keys(array_keys($v)))) > 0) {
                    return '[object Object]';
                } else {
                    $ret = array();
                    foreach ($v as $k => $vv) {
                        $ret[] = static::raw($cx, $vv);
                    }
                    return join(',', $ret);
                }
            } else {
                return 'Array';
            }
        }

        return "$v";
    }
    public static function enc($cx, $var) {
        return htmlentities(static::raw($cx, $var), ENT_QUOTES, 'UTF-8');
    }
    public static function encq($cx, $var) {
        return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(static::raw($cx, $var), ENT_QUOTES, 'UTF-8'));
    }
}
}
return function ($in = null, $options = null) {
    $helpers = array(            'feed_url' => function($options) {
        // Get hash arguments
        $author = $options['hash']['author'];
        $tag = $options['hash']['tag'];

        // Set feed options
        $feed_options = [];
        if($author) $feed_options['author'] = $author;
        if($tag) $feed_options['tag'] = $tag;

        return \Postleaf\Feed::url($feed_options);
    },
            'search_url' => function($options) {
        $query = (string) $options['hash']['query'];
        $page = (int) $options['hash']['page'];

        // Empty queries get pushed to page 1
        if(!$query) $page = 1;

        return \Postleaf\Search::url($query, $page);
    },
            'theme_url' => function($path, $options = null) {
        return $options ?
            \Postleaf\Postleaf::url('content/themes', \Postleaf\Setting::get('theme'), $path) :
            \Postleaf\Postleaf::url('content/themes', \Postleaf\Setting::get('theme'));
    },
            'url' => function($path, $options = null) {
        if(!$options) {
            $options = $path;
            $path = '';
        }

        // If this is a fully qualified URL, return is as-is
        if(preg_match('/^(http:|https:|mailto:|\/\/:)/i', $path)) {
            return $path;
        } else {
            return \Postleaf\Postleaf::url($path);
        }
    },
            'date' => function($date, $options = null) {
        // If only one argument was passed in, adjust options and set the default $date
        if(!$options) {
            $options = $date;

            // Try this.date
            if(isset($options['_this']['pub_date'])) {
                $date = $options['_this']['pub_date'];
            } else {
                // Fallback to the current date/time
                $date = date('Y-m-d H:i:s');
            }
        }

        // Determine format
        $format = isset($options['hash']['format']) ? $options['hash']['format'] : '%Y-%m-%d';

        return \Postleaf\Postleaf::strftime($format, strtotime($date));
    },
            'body_class' => function($options) {
        // Template class
        $class = $options['data']['template'] . '-template';

        // Homepage class
        if(\Postleaf\Postleaf::isHomepage()) {
            $class .= ' homepage';
        }

        // Pagination class
        if(isset($options['_this']['pagination'])) {
            $class .= ' page-' . $options['_this']['pagination']['current_page'];
        }

        return $class;
    },
            'navigation' => function($options) {
        // Decode nav from settings
        $items = (array) json_decode(\Postleaf\Setting::get('navigation'), true);

        // Generate `slug` and `current` values for each nav item
        foreach($items as $key => $value) {
            $items[$key]['slug'] = \Postleaf\Postleaf::slug($value['label']);
            $items[$key]['current'] = \Postleaf\Postleaf::isCurrentUrl($value['link']);
        }

        if(count($items)) {
            return $options['fn']([
                'items' => $items
            ]);
        } else {
            // No items, do {{else}}
            return $options['inverse'] ? $options['inverse']() : '';
        }
    },
            'postleaf_foot' => function($options) {
        $html = '';

        // If we're editing a post, add required code
        if($options['data']['meta']['editable']) {
            // Inject TinyMCE
            $html .=
                '<!--{{postleaf_foot}}-->' .
                '<script src="' . htmlspecialchars(
                    \Postleaf\Postleaf::url(
                        'source/vendor/tinymce/tinymce/tinymce.min.js?v=' .
                        $options['data']['postleaf']['version']
                    )
                ) . '"></script>';
        }

        // Inject foot code
        $html .= "\n" . \Postleaf\Setting::get('foot_code');

        // Inject admin toolbar if the user is logged in and the post isn't editable or a preview
        if(
            \Postleaf\Session::isAuthenticated() &&
            !$options['data']['meta']['editable'] &&
            !$options['data']['meta']['preview']
        ) {
            // Render it
            $html .= \Postleaf\Renderer::render([
                'template' => \Postleaf\Postleaf::path(
                    'source/templates/partials/admin-toolbar.hbs'
                ),
                'data' => [
                    'items' => \Postleaf\Admin::getAdminToolbarItems(
                        $options['data']['template'],
                        $options['_this']
                    )
                ],
                'helpers' => ['url', 'utility', 'theme']
            ]);
        }

        // Return raw HTML
        return new LS($html);
    },
            'postleaf_head' => function($options) {
        $html = '';

        // If we're editing a post, add required code
        if($options['data']['meta']['editable']) {
            // Inject Postleaf data and editor stylesheet
            $html .=
                '<!--{{postleaf_head}}-->' .
                '<script>window.postleaf = true;</script>' .
                '<link rel="stylesheet" href="' . htmlspecialchars(
                    \Postleaf\Postleaf::url(
                        'source/assets/css/editor.css?v=' .
                        $options['data']['postleaf']['version']
                    )
                ) . '">';
        }

        // Inject admin toolbar styles if the user is logged in and it's not a preview/editable post
        if(
            \Postleaf\Session::isAuthenticated() &&
            !$options['data']['meta']['editable'] &&
            !$options['data']['meta']['preview']
        ) {
            $html .=
                '<link rel="stylesheet" href="' .
                htmlspecialchars(
                    \Postleaf\Postleaf::url(
                        'source/assets/css/admin-toolbar.css?v=' .
                        $options['data']['postleaf']['version']
                    )
                ) .
                '">';
        }

        // Inject head code
        $html .= "\n" . \Postleaf\Setting::get('head_code');

        // Inject JSON linked data (schema.org)
        if(isset($options['data']['meta']['ld_json'])) {
            $html .=
                "\n<script type=\"application/ld+json\">" .
                json_encode($options['data']['meta']['ld_json'], JSON_PRETTY_PRINT) .
                "</script>";
        }

        // Inject Open Graph data
        if(is_array($options['data']['meta']['open_graph'])) {
            foreach($options['data']['meta']['open_graph'] as $key => $value) {
                if($value === null) continue;
                $html .= "\n<meta property=\"" . htmlspecialchars($key) . "\" content=\"" . htmlspecialchars($value) . "\">";
            }
        }

        // Inject Twitter Card data
        if(is_array($options['data']['meta']['twitter_card'])) {
            foreach($options['data']['meta']['twitter_card'] as $key => $value) {
                if($value === null) continue;
                $html .= "\n<meta name=\"" . htmlspecialchars($key) . "\" content=\"" . htmlspecialchars($value) . "\">";
            }
        }

        // Return raw HTML
        return new LS($html);
    },
);
    $partials = array('navigation' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<div class="nav">
',$sp,'
',$sp,'    <form class="search" action="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'search_url', array(array(),array()), 'encq', $in)),'" autocomplete="off">
',$sp,'        <input type="search" name="s" placeholder="Search" class="form-control">
',$sp,'    </form>
',$sp,'
',$sp,'    <nav>
',$sp,'',lcr57ec36eba4ccdhbch($cx, 'navigation', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57ec36eba4ccdsec($cx, lcr57ec36eba4ccdv($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a class="nav-',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdv($cx, $in, isset($in) ? $in : null, array('slug'))),' ';if (lcr57ec36eba4ccdifvar($cx, lcr57ec36eba4ccdv($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current';}else{echo '';}echo '" href="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'url', array(array(lcr57ec36eba4ccdv($cx, $in, isset($in) ? $in : null, array('link'))),array()), 'encq', $in)),'">
',$sp,'                    ',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdv($cx, $in, isset($in) ? $in : null, array('label'))),'
',$sp,'                </a>
',$sp,'';}),'';}),'    </nav>
',$sp,'
',$sp,'    <a class="feed" href="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'feed_url', array(array(),array()), 'encq', $in)),'">
',$sp,'        RSS Feed
',$sp,'    </a>
',$sp,'
',$sp,'</div>';return ob_get_clean();},
'header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('meta','title'))),'</title>
',$sp,'    <meta name="description" content="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('meta','description'))),'">
',$sp,'
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta name="viewport" content="width=device-width,initial-scale=1.0">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'
',$sp,'    ',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'postleaf_head', array(array(),array()), 'encq', $in)),'
',$sp,'
',$sp,'    <link rel="shortcut icon" href="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'url', array(array(lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon'))),array()), 'encq', $in)),'">
',$sp,'
',$sp,'    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" integrity="sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY" crossorigin="anonymous">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather:300,700%7CLato:300,700">
',$sp,'
',$sp,'    <link rel="stylesheet" href="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'theme_url', array(array('css/theme.css'),array()), 'encq', $in)),'">
',$sp,'</head>
',$sp,'
',$sp,'<body class="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'body_class', array(array(),array()), 'encq', $in)),'">
',$sp,'
',$sp,'',lcr57ec36eba4ccdp($cx, 'navigation', array(array($in),array()),0, '    '),'
',$sp,'    <div class="body-wrap">
',$sp,'
',$sp,'';if (lcr57ec36eba4ccdifvar($cx, lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo')), false)){echo '            <a href="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'url', array(array(),array()), 'encq', $in)),'" class="logo">
',$sp,'                <img src="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'url', array(array(lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','logo'))),array()), 'encq', $in)),'" alt="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'">
',$sp,'            </a>
',$sp,'';}else{echo '';}echo '
',$sp,'        <a href="#" class="nav-toggle">Menu</a>';return ob_get_clean();},
'search-form' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<div class="search">
',$sp,'    <form class="form-inline" action="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'search_url', array(array(),array()), 'encq', $in)),'" method="GET" autocomplete="off">
',$sp,'        <input type="search" name="s" class="form-control" placeholder="Search">
',$sp,'        <button type="submit" class="btn btn-secondary">Go</button>
',$sp,'    </form>
',$sp,'</div>';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'        <footer>
',$sp,'            <div class="container">
',$sp,'                <div class="row">
',$sp,'                    <div class="col-xs-6">
',$sp,'                        <a href="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'url', array(array(),array()), 'encq', $in)),'">',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</a> &copy;',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'date', array(array(),array('format'=>'%Y')), 'encq', $in)),'
',$sp,'                    </div>
',$sp,'                    <div class="col-xs-6 text-xs-right">
',$sp,'                        <a href="http://colegiulunirea.ro/">Colegiul Național „Unirea”</a>
',$sp,'                    </div>
',$sp,'                </div>
',$sp,'            </div>
',$sp,'        </footer>
',$sp,'
',$sp,'    </div> 
',$sp,'
',$sp,'    ',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'postleaf_foot', array(array(),array()), 'encq', $in)),'
',$sp,'
',$sp,'    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
',$sp,'    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
',$sp,'    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js" integrity="sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD" crossorigin="anonymous"></script>
',$sp,'
',$sp,'    <script src="',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'theme_url', array(array('js/theme.js'),array()), 'encq', $in)),'"></script>
',$sp,'</body>
',$sp,'</html>';return ob_get_clean();});
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'spvar' => true,
            'prop' => true,
            'method' => false,
            'lambda' => false,
            'mustlok' => false,
            'mustlam' => false,
            'echo' => true,
            'partnc' => false,
            'knohlp' => false,
            'debug' => isset($options['debug']) ? $options['debug'] : 1,
        ),
        'constants' =>  array(
            'DEBUG_ERROR_LOG' => 1,
            'DEBUG_ERROR_EXCEPTION' => 2,
            'DEBUG_TAGS' => 4,
            'DEBUG_TAGS_ANSI' => 12,
            'DEBUG_TAGS_HTML' => 20,
        ),
        'helpers' => isset($options['helpers']) ? array_merge($helpers, $options['helpers']) : $helpers,
        'partials' => isset($options['partials']) ? array_merge($partials, $options['partials']) : $partials,
        'scopes' => array(),
        'sp_vars' => isset($options['data']) ? array_merge(array('root' => $in), $options['data']) : array('root' => $in),
        'blparam' => array(),
        'partialid' => 0,
        'runtime' => '\LightnCandy\Runtime',
    );
    
    ob_start();echo '',lcr57ec36eba4ccdp($cx, 'header', array(array($in),array()),0),'
<header class="cover" ';if (lcr57ec36eba4ccdifvar($cx, lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','cover')), false)){echo 'style="background-image: url(\'',lcr57ec36eba4ccdencq($cx, lcr57ec36eba4ccdhbch($cx, 'url', array(array(lcr57ec36eba4ccdv($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','cover'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '>
    <div class="content">
        <h1 class="title">Page Not Found</h1>
        <div class="subtitle">The page you requested could not be found</div>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="col-md-8 push-md-2 col-sm-10 push-sm-1 text-xs-center">
            <h2>Search</h2>
            <p>Try searching for what you’re looking for.</p>

',lcr57ec36eba4ccdp($cx, 'search-form', array(array($in),array()),0, '            '),'        </div>
    </div>
</div>

',lcr57ec36eba4ccdp($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};