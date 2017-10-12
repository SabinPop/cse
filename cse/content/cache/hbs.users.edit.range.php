<?php  function lcr57fdc58df12b9v($cx, $in, $base, $path, $args = null) {
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
   lcr57fdc58df12b9err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57fdc58df12b9encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57fdc58df12b9raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57fdc58df12b9hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr57fdc58df12b9m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57fdc58df12b9m($cx, $context, $ex) : $context);
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
   lcr57fdc58df12b9err($cx, $e);
  }

  return $r;
 }
 function lcr57fdc58df12b9ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57fdc58df12b9sec($cx, $v, $bp, $in, $each, $cb, $else = null) {
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
     $raw = lcr57fdc58df12b9m($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr57fdc58df12b9m($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
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
 function lcr57fdc58df12b9p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57fdc58df12b9err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57fdc58df12b9m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57fdc58df12b9err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57fdc58df12b9raw($cx, $v) {
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
      $ret[] = lcr57fdc58df12b9raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr57fdc58df12b9m($cx, $a, $b) {
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
            'author_url' => function($author, $options = null) {
        if(!$options) {
            $options = $author;

            if(isset($options['_this']['author'])) {
                // Try this.author
                $author = $options['_this']['author'];
            } elseif(isset($options['_this']['slug'])) {
                // Try this.slug
                $author = $options['_this']['slug'];
            } else {
                // Nothing to fall back to
                return '';
            }
        }

        return \Postleaf\User::url($author, (int) $options['hash']['page']);
    },
            'blog_url' => function($path, $options = null) {
        return \Postleaf\Blog::url($options['hash']['page']);
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
',$sp,'    <title>',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('title'))),' &middot; ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</title>
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'    <meta name="apple-mobile-web-app-title" content="Postleaf">
',$sp,'    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1, minimal-ui">
',$sp,'    <meta name="postleaf:language" data-cancel="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'" data-ok="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('ok'),array()), 'encq', $in)),'" data-changes-saved="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('your_changes_have_been_saved'),array()), 'encq', $in)),'">
',$sp,'    <meta name="postleaf:template" content="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template'))),'">
',$sp,'    <meta name="postleaf:url" data-base="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" data-admin="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'admin_url', array(array(),array()), 'encq', $in)),'">
',$sp,'',lcr57fdc58df12b9hbch($cx, 'either', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')),'source/assets/img/logo-color.png'),array()), $in, false, function($cx, $in)use($sp){echo '    <link rel="apple-touch-icon" href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'    <link rel="shortcut icon" href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'';}),'    <link rel="stylesheet" href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array('source/assets/css/lib.css'),array()), 'encq', $in)),'?v=',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'">
',$sp,'    ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'admin_styles', array(array(),array()), 'encq', $in)),'
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,600,600italic">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono:300">
',$sp,'</head>
',$sp,'<body class="admin';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('body_class')), false)){echo ' ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('body_class'))),'';}else{echo '';}echo ' preload">';return ob_get_clean();},
'menu' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<nav class="main-menu">
',$sp,'    <a aria-label="Postleaf" href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" title="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'" data-toggle="tooltip">
',$sp,'        <img src="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array('source/assets/img/1.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'    </a>
',$sp,'
',$sp,'',lcr57fdc58df12b9hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57fdc58df12b9sec($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '            <a href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('link'))),'" title="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('title'))),'" data-toggle="tooltip" class="';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                    <img src="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                    <i class="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '            </a>
',$sp,'';}),'';}),'</nav>
',$sp,'
',$sp,'<nav class="mobile-menu">
',$sp,'    <div class="mobile-menu-header">
',$sp,'        <a class="mobile-menu-logo" href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'blog_url', array(array(),array()), 'encq', $in)),'">
',$sp,'            <img src="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array('source/assets/img/12.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'        </a>
',$sp,'        <span class="mobile-menu-toggle" href="#">
',$sp,'            <i class="fa fa-navicon"></i>
',$sp,'        </span>
',$sp,'        <div class="mobile-menu-title">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('title'))),'</div>
',$sp,'    </div>
',$sp,'    <div class="mobile-menu-items">
',$sp,'',lcr57fdc58df12b9hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr57fdc58df12b9sec($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('link'))),'" class="';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                        <img src="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                        <i class="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '                    <span class="description">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('title'))),'</span>
',$sp,'                </a>
',$sp,'';}),'';}),'    </div>
',$sp,'</nav>';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user')), false)){echo '<div class="locater-overlay" hidden></div>
',$sp,'<div class="locater" hidden>
',$sp,'    <div class="form-group">
',$sp,'        <div class="inner-addon-group">
',$sp,'            <span class="inner-addon"><i class="fa fa-search"></i></span>
',$sp,'            <input type="text" class="form-control locater-input" placeholder="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'">
',$sp,'        </div>
',$sp,'    </div>
',$sp,'    <div class="locater-results"></div>
',$sp,'</div>
',$sp,'';}else{echo '';}echo '
',$sp,'<script src="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array('source/assets/js/lib.min.js'),array()), 'encq', $in)),'?v=',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'"></script>
',$sp,'',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'admin_scripts', array(array(),array()), 'encq', $in)),'
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
    
    ob_start();echo '',lcr57fdc58df12b9p($cx, 'header', array(array($in),array()),0),'
',lcr57fdc58df12b9p($cx, 'menu', array(array($in),array()),0),'
<div class="container-fluid">
    <div class="top-toolbar">
        <div class="top-toolbar-section col-lg-4 col-sm-6">
            <h1>
',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','role')),'in','owner,admin'),array()), $in, false, function($cx, $in) {echo '                    <a href="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'admin_url', array(array('users'),array()), 'encq', $in)),'">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('users'),array()), 'encq', $in)),'</a>
                    <i class="fa fa-angle-right"></i>
';}),'',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template')),'users.new'),array()), $in, false, function($cx, $in) {echo '                    ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('new_user'),array()), 'encq', $in)),'
';}, function($cx, $in) {echo '                    ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','name'))),'
';}),'            </h1>
        </div>
        <div class="top-toolbar-section col-lg-8 col-sm-6 text-sm-right text-xs-left">
',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template')),'users.edit'),array()), $in, false, function($cx, $in) {echo '            <div class="btn-group m-r-1">
                <button type="button" class="open btn btn-secondary" data-url="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'author_url', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','slug'))),array()), 'encq', $in)),'">
                    <i class="fa fa-external-link"></i>
                </button>
                <button type="button" class="delete btn btn-secondary" data-confirm="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('delete_this_user'),array()), 'encq', $in)),'" ',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','role')),'owner'),array()), $in, false, function($cx, $in) {echo ' disabled';}),'>
                    <i class="fa fa-trash-o"></i>
                </button>
            </div>
';}),'            <button type="button" class="submit btn btn-primary">
                ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('save'),array()), 'encq', $in)),'
            </button>
        </div>
    </div>
</div>

<div class="main-container stretch-down">
    <div class="row">
        <div
            class="cover"
';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','cover')), false)){echo '                ';if (lcr57fdc58df12b9ifvar($cx, $in, false)){echo 'style="background-image:url(\'',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','cover'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '
';}else{echo '';}echo '        >
            <div class="avatar">
                <img class="image" src="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'url', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','avatar'))),array()), 'encq', $in)),'"';if (!lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','avatar')), false)){echo ' hidden';}else{echo '';}echo '>
                <i class="none fa fa-user"';if (lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','avatar')), false)){echo ' hidden';}else{echo '';}echo '></i>
                <label class="upload-avatar">
                    <i class="fa fa-fw fa-camera"></i>
                    <input type="file" accept="image/*" style="display: none;">
                </label>
            </div>
            <div class="cover-controls">
                <label class="upload-cover btn btn-translucent-dark">
                    <i class="fa fa-fw fa-camera"></i>
                    <input type="file" accept="image/*" style="display: none;">
                </label>
                <button type="button" class="remove-cover btn btn-translucent-dark"';if (!lcr57fdc58df12b9ifvar($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','cover')), false)){echo ' hidden';}else{echo '';}echo '>
                    <i class="fa fa-fw fa-remove"></i>
                </button>
            </div>
        </div>
    </div>

    <form class="user-form" autocomplete="off" data-redirect="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('redirect'))),'">
        <div class="row">
            <div class="col-lg-6 push-lg-3 col-md-8 push-md-2 col-sm-10 push-sm-1">
                <div class="form-group m-t-2">
                    <label for="name">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('name'),array()), 'encq', $in)),'</label>
                    <input class="form-control" type="text" name="name" id="name" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','name'))),'" autofocus>
                </div>
                <div class="form-group">
                    <label for="email">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('email'),array()), 'encq', $in)),'</label>
                    <div class="inner-addon-group">
                        <span class="inner-addon"><i class="fa fa-envelope-o"></i></span>
                        <input class="form-control" type="email" name="email" id="email" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','email'))),'">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="username">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('username'),array()), 'encq', $in)),'</label>
                            <div class="inner-addon-group">
                                <span class="inner-addon"><i class="fa fa-user"></i></span>
                                <input class="form-control" type="text" name="username" id="username" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','slug'))),'">
                            </div>
                        </div>
                    </div>
',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','role')),'in','owner,admin'),array()), $in, false, function($cx, $in) {echo '                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="role">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('role'),array()), 'encq', $in)),'</label>
                            <select class="form-control" name="role" id="role">
',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','role')),'owner'),array()), $in, false, function($cx, $in) {echo '                                    <option value="owner"',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','role')),'owner'),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('owner'),array()), 'encq', $in)),'</option>
';}, function($cx, $in) {echo '                                    <option value="author"',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','role')),'author'),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('author'),array()), 'encq', $in)),'</option>
                                    <option value="editor"',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','role')),'editor'),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('editor'),array()), 'encq', $in)),'</option>
                                    <option value="admin"',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','role')),'admin'),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('administrator'),array()), 'encq', $in)),'</option>
';}),'                            </select>
                        </div>
                    </div>
';}),'                </div>

                <h3 class="m-t-3">
',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template')),'users.new'),array()), $in, false, function($cx, $in) {echo '                        ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('password'),array()), 'encq', $in)),'
';}, function($cx, $in) {echo '                        ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('change_password'),array()), 'encq', $in)),'
';}),'                </h3>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="password">
',lcr57fdc58df12b9hbch($cx, 'is', array(array(lcr57fdc58df12b9v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template')),'users.new'),array()), $in, false, function($cx, $in) {echo '                                    ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('password'),array()), 'encq', $in)),'
';}, function($cx, $in) {echo '                                    ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('new_password'),array()), 'encq', $in)),'
';}),'                            </label>
                            <input class="form-control" type="password" name="password" id="password">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="verify-password">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('verify_password'),array()), 'encq', $in)),'</label>
                            <input class="form-control" type="password" name="verify-password" id="verify-password">
                        </div>
                    </div>
                </div>

                <h3 class="m-t-3">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('about'),array()), 'encq', $in)),'</h3>
                <div class="form-group">
                    <label for="website">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('website'),array()), 'encq', $in)),'</label>
                    <div class="inner-addon-group">
                        <span class="inner-addon"><i class="fa fa-link"></i></span>
                        <input class="form-control" type="url" name="website" id="website" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','website'))),'">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="location">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('location'),array()), 'encq', $in)),'</label>
                            <div class="inner-addon-group">
                                <span class="inner-addon"><i class="fa fa-map-marker"></i></span>
                                <input class="form-control" type="text" name="location" id="location" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','location'))),'">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="twitter">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('twitter'),array()), 'encq', $in)),'</label>
                        <div class="inner-addon-group">
                            <span class="inner-addon"><i class="fa fa-twitter"></i></span>
                            <input class="form-control" type="text" name="twitter" id="twitter" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','twitter'))),'">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bio">
                        ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('bio'),array()), 'encq', $in)),'
                    </label>
                    <div class="tag-cover">
                        <span class="tag tag-default tag-tr tag-outside">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('markdown'),array()), 'encq', $in)),'</span>
                        <textarea class="form-control" name="bio" id="bio" rows="6">',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','bio'))),'</textarea>
                    </div>
                    <small class="text-muted">
                        ',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9hbch($cx, 'L', array(array('share_a_sentence_or_two_about_yourself'),array()), 'encq', $in)),'
                    </small>
                </div>
            </div>
        </div>

        <input type="hidden" name="user" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','slug'))),'">
        <input type="hidden" name="avatar" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','avatar'))),'">
        <input type="hidden" name="cover" value="',lcr57fdc58df12b9encq($cx, lcr57fdc58df12b9v($cx, $in, isset($in) ? $in : null, array('user','cover'))),'">
        <button type="submit" hidden></button>
    </form>
</div>

',lcr57fdc58df12b9p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};