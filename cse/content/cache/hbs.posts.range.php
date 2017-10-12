<?php  function lcr57fdc5ac347f7v($cx, $in, $base, $path, $args = null) {
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
   lcr57fdc5ac347f7err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57fdc5ac347f7encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57fdc5ac347f7raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57fdc5ac347f7hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr57fdc5ac347f7m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57fdc5ac347f7m($cx, $context, $ex) : $context);
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
   lcr57fdc5ac347f7err($cx, $e);
  }

  return $r;
 }
 function lcr57fdc5ac347f7ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57fdc5ac347f7sec($cx, $v, $bp, $in, $each, $cb, $else = null) {
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
     $raw = lcr57fdc5ac347f7m($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr57fdc5ac347f7m($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
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
 function lcr57fdc5ac347f7p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57fdc5ac347f7err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57fdc5ac347f7m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57fdc5ac347f7raw($cx, $v) {
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
      $ret[] = lcr57fdc5ac347f7raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr57fdc5ac347f7err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57fdc5ac347f7m($cx, $a, $b) {
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
    $helpers = array(            'admin_author' => function($slug, $options = null) {
        $author = \Postleaf\User::get($slug);

        // Do {{else}} if no author is found
        if(!$author) {
            return $options['inverse'] ? $options['inverse']() : '';
        }

        // Remove sensitive data
        unset($author['password'], $author['reset_token']);

        return $options['fn']($author);
    },
            'admin_menu' => function() {
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
            'post_url' => function($slug, $options = null) {
        if(!$options) {
            $options = $slug;

            if(isset($options['_this']['slug'])) {
                // Try this.slug
                $slug = $options['_this']['slug'];
            } else {
                return '';
            }
        }

        return \Postleaf\Post::url($slug);
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
            'is' => function() {
        $args = func_get_args();
        $options = end($args);

        switch(count($args) - 1) {
            // One variable
            case 1:
                $left = true;
                $right = !!$args[0];
                $operator = '==';
                break;

            // Two variables
            case 2:
                $left = $args[0];
                $operator = '==';
                $right = $args[1];
                break;

            // Two variables + operator
            case 3:
                $left = $args[0];
                $operator = $args[1];
                $right = $args[2];
        }

        // Compare values
        switch(strtolower($operator)) {
            case '>':
                $is = $left > $right;
                break;
            case '>=':
                $is = $left >= $right;
                break;
            case '<':
                $is = $left < $right;
                break;
            case '<=':
                $is = $left <= $right;
                break;
            case '===':
                $is = $left === $right;
                break;
            case '&&':
            case 'and':
                $is = $left && $right;
                break;
            case '||':
            case 'or':
                $is = ($left || $right);
                break;
            case 'xor':
                $is = ($left xor $right);
                break;
            case '!=':
            case 'not':
                $is = $left != $right;
                break;
            case '!==':
                $is = $left !== $right;
                break;
            case 'in':
            case 'not in':
                $is = true;
                if(!is_array($right)) {
                    // Split CSV into an array
                    $right = explode(',', (string) $right);
                    $right = array_map(function($a) {
                        return trim($a);
                    }, $right);
                }
                $is = in_array($left, $right);
                if(strtolower($operator) === 'not in') $is = !$is;
                break;
            case 'type':
                if($right === 'array') $is = is_array($left);
                if($right === 'string') $is = is_string($left);
                break;
            default:
                $is = $left == $right;
                break;
        }

        if($is) {
            return $options['fn']();
        } else {
            return $options['inverse'] ? $options['inverse']() : '';
        }
    },
            'L' => function($term, $options) {
        return \Postleaf\Language::term($term, $options['hash']);
    },
);
    $partials = array('header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('title'))),' &middot; ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</title>
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'    <meta name="apple-mobile-web-app-title" content="Postleaf">
',$sp,'    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1, minimal-ui">
',$sp,'    <meta name="postleaf:language" data-cancel="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'" data-ok="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('ok'),array()), 'encq', $in)),'" data-changes-saved="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('your_changes_have_been_saved'),array()), 'encq', $in)),'">
',$sp,'    <meta name="postleaf:template" content="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template'))),'">
',$sp,'    <meta name="postleaf:url" data-base="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" data-admin="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'admin_url', array(array(),array()), 'encq', $in)),'">
',$sp,'',lcr57fdc5ac347f7hbch($cx, 'either', array(array(lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')),'source/assets/img/logo-color.png'),array()), $in, false, function($cx, $in)use($sp){echo '    <link rel="apple-touch-icon" href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'    <link rel="shortcut icon" href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'';}),'    <link rel="stylesheet" href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array('source/assets/css/lib.css'),array()), 'encq', $in)),'?v=',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'">
',$sp,'    ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'admin_styles', array(array(),array()), 'encq', $in)),'
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,600,600italic">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono:300">
',$sp,'</head>
',$sp,'<body class="admin';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('body_class')), false)){echo ' ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('body_class'))),'';}else{echo '';}echo ' preload">';return ob_get_clean();},
'menu' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<nav class="main-menu">
',$sp,'    <a aria-label="Postleaf" href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" title="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'" data-toggle="tooltip">
',$sp,'        <img src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array('source/assets/img/1.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'    </a>
',$sp,'
',$sp,'',lcr57fdc5ac347f7hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57fdc5ac347f7sec($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '            <a href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('link'))),'" title="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('title'))),'" data-toggle="tooltip" class="';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                    <img src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                    <i class="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '            </a>
',$sp,'';}),'';}),'</nav>
',$sp,'
',$sp,'<nav class="mobile-menu">
',$sp,'    <div class="mobile-menu-header">
',$sp,'        <a class="mobile-menu-logo" href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'blog_url', array(array(),array()), 'encq', $in)),'">
',$sp,'            <img src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array('source/assets/img/12.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'        </a>
',$sp,'        <span class="mobile-menu-toggle" href="#">
',$sp,'            <i class="fa fa-navicon"></i>
',$sp,'        </span>
',$sp,'        <div class="mobile-menu-title">',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('title'))),'</div>
',$sp,'    </div>
',$sp,'    <div class="mobile-menu-items">
',$sp,'',lcr57fdc5ac347f7hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57fdc5ac347f7sec($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('link'))),'" class="';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                        <img src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                        <i class="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '                    <span class="description">',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('title'))),'</span>
',$sp,'                </a>
',$sp,'';}),'';}),'    </div>
',$sp,'</nav>';return ob_get_clean();},
'post-list' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'',lcr57fdc5ac347f7sec($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('posts')), null, $in, true, function($cx, $in)use($sp){echo '    <a
',$sp,'        class="post-list-item"
',$sp,'        href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'admin_url', array(array('posts/'),array()), 'encq', $in)),'',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'encode', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('slug'))),array()), 'encq', $in)),'"
',$sp,'        data-slug="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('slug'))),'"
',$sp,'        data-url="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'post_url', array(array(),array()), 'encq', $in)),'?preview"
',$sp,'    >
',$sp,'',lcr57fdc5ac347f7hbch($cx, 'is', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('status')),'draft'),array()), $in, false, function($cx, $in)use($sp){echo '            <span class="tag tag-warning">',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('draft'),array()), 'encq', $in)),'</span>
',$sp,'';}),'',lcr57fdc5ac347f7hbch($cx, 'is', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('page'))),array()), $in, false, function($cx, $in)use($sp){echo '            <span class="tag tag-primary">',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('page'),array()), 'encq', $in)),'</span>
',$sp,'';}),'',lcr57fdc5ac347f7hbch($cx, 'is', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('featured'))),array()), $in, false, function($cx, $in)use($sp){echo '            <span class="tag tag-success">',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('featured'),array()), 'encq', $in)),'</span>
',$sp,'';}),'',lcr57fdc5ac347f7hbch($cx, 'is', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('sticky'))),array()), $in, false, function($cx, $in)use($sp){echo '            <span class="tag tag-info">',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('sticky'),array()), 'encq', $in)),'</span>
',$sp,'';}),'        <div class="title">
',$sp,'            ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('title'))),'
',$sp,'        </div>
',$sp,'        <div class="description">
',$sp,'',lcr57fdc5ac347f7hbch($cx, 'admin_author', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('author'))),array()), $in, false, function($cx, $in)use($sp){echo '';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                    <img class="avatar" src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array(lcr57fdc5ac347f7v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'">
',$sp,'';}else{echo '';}echo '';}),'            <span class="date">
',$sp,'                ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'date', array(array(),array('format'=>'%B %e, %Y')), 'encq', $in)),'
',$sp,'            </span>
',$sp,'        </div>
',$sp,'    </a>
',$sp,'';}, function($cx, $in)use($sp){echo '    <div class="post-list-none valign">
',$sp,'        <div class="valign-middle">
',$sp,'            ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('no_posts'),array()), 'encq', $in)),'
',$sp,'        </div>
',$sp,'    </div>
',$sp,'';}),'';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr57fdc5ac347f7ifvar($cx, lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user')), false)){echo '<div class="locater-overlay" hidden></div>
',$sp,'<div class="locater" hidden>
',$sp,'    <div class="form-group">
',$sp,'        <div class="inner-addon-group">
',$sp,'            <span class="inner-addon"><i class="fa fa-search"></i></span>
',$sp,'            <input type="text" class="form-control locater-input" placeholder="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'">
',$sp,'        </div>
',$sp,'    </div>
',$sp,'    <div class="locater-results"></div>
',$sp,'</div>
',$sp,'';}else{echo '';}echo '
',$sp,'<script src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array('source/assets/js/lib.min.js'),array()), 'encq', $in)),'?v=',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'"></script>
',$sp,'',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'admin_scripts', array(array(),array()), 'encq', $in)),'
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
    
    ob_start();echo '',lcr57fdc5ac347f7p($cx, 'header', array(array($in),array()),0),'
',lcr57fdc5ac347f7p($cx, 'menu', array(array($in),array()),0),'
<div class="container-fluid">
    <div class="top-toolbar">
        <div class="top-toolbar-section col-lg-4 col-sm-6">
            <div class="inner-addon-group">
                <span class="inner-addon"><i class="fa fa-search"></i></span>
                <input
                    class="post-search form-control"
                    type="search"
                    placeholder="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'"
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
                <button type="button" class="delete btn btn-secondary" disabled data-confirm="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('delete_the_selected_posts'),array()), 'encq', $in)),'">
                    <i class="fa fa-trash-o"></i>
                </button>
            </div>
            <a href="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'admin_url', array(array('posts/new'),array()), 'encq', $in)),'" class="btn btn-success">
                <i class="fa fa-plus"></i>
            </a>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="row">
        <div class="col-md-5">
            <div class="post-list stretch-down">
',lcr57fdc5ac347f7p($cx, 'post-list', array(array($in),array()),0, '                '),'            </div>
        </div>
        <div class="col-md-7 hidden-sm-down">
            <div class="preview stretch-down">
                <div class="preview-panel preview-none">
                    <div class="valign">
                        <div class="valign-middle">
                            ',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('no_posts_selected'),array()), 'encq', $in)),'
                        </div>
                    </div>
                </div>
                <div class="preview-panel preview-one" hidden>
                    <div class="preview-frame-wrap">
                        <iframe class="preview-frame" src="',lcr57fdc5ac347f7encq($cx, lcr57fdc5ac347f7hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" tabindex="-1"></iframe>
                    </div>
                </div>
                <div class="preview-panel preview-multiple" hidden>
                    <div class="valign">
                        <div class="valign-middle">
                            <i class="fa fa-files-o"></i>
                            <div class="m-y-2">
                                ',lcr57fdc5ac347f7raw($cx, lcr57fdc5ac347f7hbch($cx, 'L', array(array('{n}_posts_selected'),array('n'=>'<span class="num-selected"></span>')), 'raw', $in)),'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="preview-panel preview-loader" hidden>
                    <div class="valign">
                        <div class="valign-middle">
                            <i class="loader"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

',lcr57fdc5ac347f7p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};