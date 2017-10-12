<?php  function lcr57e95e8b65f11v($cx, $in, $base, $path, $args = null) {
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
   lcr57e95e8b65f11err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57e95e8b65f11encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57e95e8b65f11raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57e95e8b65f11hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr57e95e8b65f11m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57e95e8b65f11m($cx, $context, $ex) : $context);
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
   lcr57e95e8b65f11err($cx, $e);
  }

  return $r;
 }
 function lcr57e95e8b65f11ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57e95e8b65f11sec($cx, $v, $bp, $in, $each, $cb, $else = null) {
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
     $raw = lcr57e95e8b65f11m($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr57e95e8b65f11m($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
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
 function lcr57e95e8b65f11p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57e95e8b65f11err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57e95e8b65f11m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57e95e8b65f11err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57e95e8b65f11raw($cx, $v) {
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
      $ret[] = lcr57e95e8b65f11raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr57e95e8b65f11m($cx, $a, $b) {
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
    $helpers = array(            'admin_menu' => function() {
        $args = func_get_args();
        $options = end($args);
        $items = \Postleaf\Admin::getMenuItems();

        // Generate `current` value for each item
        foreach($items as $key => $value) {
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
            'admin_scripts' => function() {
        $args = func_get_args();
        $options = end($args);
        $html = '';

        foreach((array) $options['_this']['scripts'] as $script) {
            // If this is a fully qualified URL, return is as-is
            if(preg_match('/^(http:|https:|mailto:|\/\/:)/i', $script)) {
                $src = $script;
            } else {
                $src =
                    self::url('source/assets/js', $script) . '?v=' .
                    $options['data']['postleaf']['version'];
            }
            $html .= '<script src="' . htmlspecialchars($src) . '"></script>';
        }

        return new LS($html);
    },
            'admin_styles' => function() {
        $args = func_get_args();
        $options = end($args);
        $html = '';

        foreach((array) $options['_this']['styles'] as $style) {
            $href =
                self::url('source/assets/css', $style) . '?v=' .
                $options['data']['postleaf']['version'];
            $html .= '<link rel="stylesheet" href="' . htmlspecialchars($href) . '">';
        }

        return new LS($html);
    },
            'admin_url' => function($path, $options = null) {
        if(!$options) {
            $options = $path;
            $path = null;
        }

        // Add hash attributes as query string data
        if(is_array($options) && count($options['hash'])) {
            $path = rtrim($path, '/') . '/?' . http_build_query($options['hash'], null, '&', PHP_QUERY_RFC3986);
        }

        return \Postleaf\Admin::url($path);
    },
            'blog_url' => function($path, $options = null) {
        return \Postleaf\Blog::url($options['hash']['page']);
    },
            'tag_url' => function($tag, $options = null) {
        if(!$options) {
            $options = $tag;

            if(isset($options['_this']['slug'])) {
                // Try this.slug
                $tag = $options['_this']['slug'];
            } else {
                return '';
            }
        }

        return \Postleaf\Tag::url($tag, (int) $options['hash']['page']);
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
            'either' => function() {
        $args = func_get_args();
        $options = end($args);

        // Loop through each argument and look for a truthy value
        for($i = 0; $i < count($args) - 1; $i++) {
            if($args[$i]) {
                return $options['fn']($args[$i]);
            }
        }

        // Do {{else}} if there aren't any truthy values
        return $options['inverse'] ? $options['inverse']() : '';
    },
            'encode' => function($string) {
        return rawurlencode($string);
    },
            'L' => function($term, $options) {
        return \Postleaf\Language::term($term, $options['hash']);
    },
            'markdown' => function($markdown, $options) {
        return \Postleaf\Postleaf::markdownToHtml($markdown);
    },
            'number' => function($number, $options) {
        $places = isset($options['hash']['places']) ?
            $options['hash']['places'] : 0;

        $decimal = isset($options['hash']['decimal']) ?
            $options['hash']['decimal'] : '.';

        $thousands = isset($options['hash']['thousands']) ?
            $options['hash']['thousands'] : ',';

        return number_format($number, $places, $decimal, $thousands);
    },
            'post_count' => function($options) {
        // Convert status from CSV to array and trim whitespace
        if(is_string($options['hash']['status'])) {
            $options['hash']['status'] = array_map(
                'trim',
                explode(',', $options['hash']['status'])
            );
        }

        // See Post::count() for available options
        return \Postleaf\Post::count($options['hash']);
    },
            'text' => function($text) {
        return new LS(strip_tags($text));
    },
);
    $partials = array('header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('title'))),' &middot; ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</title>
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'    <meta name="apple-mobile-web-app-title" content="Postleaf">
',$sp,'    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1, minimal-ui">
',$sp,'    <meta name="postleaf:language" data-cancel="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'" data-ok="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('ok'),array()), 'encq', $in)),'" data-changes-saved="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('your_changes_have_been_saved'),array()), 'encq', $in)),'">
',$sp,'    <meta name="postleaf:template" content="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template'))),'">
',$sp,'    <meta name="postleaf:url" data-base="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" data-admin="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'admin_url', array(array(),array()), 'encq', $in)),'">
',$sp,'',lcr57e95e8b65f11hbch($cx, 'either', array(array(lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')),'source/assets/img/logo-color.png'),array()), $in, false, function($cx, $in)use($sp){echo '    <link rel="apple-touch-icon" href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'    <link rel="shortcut icon" href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'';}),'    <link rel="stylesheet" href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array('source/assets/css/lib.css'),array()), 'encq', $in)),'?v=',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'">
',$sp,'    ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'admin_styles', array(array(),array()), 'encq', $in)),'
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,600,600italic">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono:300">
',$sp,'</head>
',$sp,'<body class="admin';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('body_class')), false)){echo ' ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('body_class'))),'';}else{echo '';}echo ' preload">';return ob_get_clean();},
'menu' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<nav class="main-menu">
',$sp,'    <a aria-label="Postleaf" href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" title="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'" data-toggle="tooltip">
',$sp,'        <img src="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array('source/assets/img/1.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'    </a>
',$sp,'
',$sp,'',lcr57e95e8b65f11hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57e95e8b65f11sec($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '            <a href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('link'))),'" title="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('title'))),'" data-toggle="tooltip" class="';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                    <img src="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array(lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                    <i class="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '            </a>
',$sp,'';}),'';}),'</nav>
',$sp,'
',$sp,'<nav class="mobile-menu">
',$sp,'    <div class="mobile-menu-header">
',$sp,'        <a class="mobile-menu-logo" href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'blog_url', array(array(),array()), 'encq', $in)),'">
',$sp,'            <img src="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array('source/assets/img/12.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'        </a>
',$sp,'        <span class="mobile-menu-toggle" href="#">
',$sp,'            <i class="fa fa-navicon"></i>
',$sp,'        </span>
',$sp,'        <div class="mobile-menu-title">',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('title'))),'</div>
',$sp,'    </div>
',$sp,'    <div class="mobile-menu-items">
',$sp,'',lcr57e95e8b65f11hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57e95e8b65f11sec($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('link'))),'" class="';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                        <img src="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array(lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                        <i class="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '                    <span class="description">',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('title'))),'</span>
',$sp,'                </a>
',$sp,'';}),'';}),'    </div>
',$sp,'</nav>';return ob_get_clean();},
'tag-list' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'',lcr57e95e8b65f11sec($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('tags')), null, $in, true, function($cx, $in)use($sp){echo '    <a
',$sp,'        class="tag-list-item"
',$sp,'        href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'admin_url', array(array('tags/'),array()), 'encq', $in)),'',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'encode', array(array(lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('slug'))),array()), 'encq', $in)),'"
',$sp,'        data-slug="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('slug'))),'"
',$sp,'        data-url="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'tag_url', array(array(),array()), 'encq', $in)),'"
',$sp,'    >
',$sp,'        <span class="post-count tag tag-default">
',$sp,'            ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'number', array(array(lcr57e95e8b65f11hbch($cx, 'post_count', array(array(),array('tag'=>lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('slug')),'status'=>null,'ignore_pages'=>false,'end_date'=>null)), 'raw', $in)),array()), 'encq', $in)),'
',$sp,'        </span>
',$sp,'        <div class="name">
',$sp,'            ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('name'))),'
',$sp,'        </div>
',$sp,'        <div class="description">
',$sp,'';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('cover')), false)){echo '                <span class="cover" style="background-image: url(\'',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array(lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('cover'))),array()), 'encq', $in)),'\');"></span>
',$sp,'';}else{echo '';}echo '            ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'text', array(array(lcr57e95e8b65f11hbch($cx, 'markdown', array(array(lcr57e95e8b65f11v($cx, $in, isset($in) ? $in : null, array('description'))),array()), 'raw', $in)),array()), 'encq', $in)),'
',$sp,'        </div>
',$sp,'    </a>
',$sp,'';}, function($cx, $in)use($sp){echo '    <div class="tag-list-none valign">
',$sp,'        <div class="valign-middle">
',$sp,'            ',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('no_tags'),array()), 'encq', $in)),'
',$sp,'        </div>
',$sp,'    </div>
',$sp,'';}),'';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr57e95e8b65f11ifvar($cx, lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user')), false)){echo '<div class="locater-overlay" hidden></div>
',$sp,'<div class="locater" hidden>
',$sp,'    <div class="form-group">
',$sp,'        <div class="inner-addon-group">
',$sp,'            <span class="inner-addon"><i class="fa fa-search"></i></span>
',$sp,'            <input type="text" class="form-control locater-input" placeholder="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'">
',$sp,'        </div>
',$sp,'    </div>
',$sp,'    <div class="locater-results"></div>
',$sp,'</div>
',$sp,'';}else{echo '';}echo '
',$sp,'<script src="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'url', array(array('source/assets/js/lib.min.js'),array()), 'encq', $in)),'?v=',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'"></script>
',$sp,'',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'admin_scripts', array(array(),array()), 'encq', $in)),'
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
    
    ob_start();echo '',lcr57e95e8b65f11p($cx, 'header', array(array($in),array()),0),'
',lcr57e95e8b65f11p($cx, 'menu', array(array($in),array()),0),'
<div class="container-fluid">
    <div class="top-toolbar">
        <div class="top-toolbar-section col-lg-4 col-sm-6">
            <div class="inner-addon-group">
                <span class="inner-addon"><i class="fa fa-search"></i></span>
                <input
                    class="tag-search form-control"
                    type="search"
                    placeholder="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'"
                    autofocus>
            </div>
        </div>
        <div class="top-toolbar-section col-lg-8 col-sm-6 text-sm-right text-xs-left">
            <div class="btn-group m-r-1">
                <button type="button" class="edit btn btn-secondary" disabled>
                    <i class="fa fa-pencil"></i>
                </button>
                <button type="button" class="open btn btn-secondary" disabled>
                    <i class="fa fa-external-link"></i>
                </button>
                <button type="button" class="delete btn btn-secondary" disabled data-confirm="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'L', array(array('delete_the_selected_tags'),array()), 'encq', $in)),'">
                    <i class="fa fa-trash-o"></i>
                </button>
            </div>
            <a href="',lcr57e95e8b65f11encq($cx, lcr57e95e8b65f11hbch($cx, 'admin_url', array(array('tags/new'),array()), 'encq', $in)),'" class="btn btn-success">
                <i class="fa fa-plus"></i>
            </a>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="row">
        <div class="tag-list stretch-down">
',lcr57e95e8b65f11p($cx, 'tag-list', array(array($in),array()),0, '            '),'        </div>
    </div>
</div>

',lcr57e95e8b65f11p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};