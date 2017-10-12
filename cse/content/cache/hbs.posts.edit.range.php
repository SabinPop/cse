<?php  function lcr58485b5e906a5v($cx, $in, $base, $path, $args = null) {
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
   lcr58485b5e906a5err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr58485b5e906a5encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr58485b5e906a5raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr58485b5e906a5hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr58485b5e906a5m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr58485b5e906a5m($cx, $context, $ex) : $context);
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
   lcr58485b5e906a5err($cx, $e);
  }

  return $r;
 }
 function lcr58485b5e906a5ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr58485b5e906a5sec($cx, $v, $bp, $in, $each, $cb, $else = null) {
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
     $raw = lcr58485b5e906a5m($cx, $raw, array($bp[0] => $raw));
    }
    if (isset($bp[1])) {
     $raw = lcr58485b5e906a5m($cx, $raw, array($bp[1] => $cx['sp_vars']['index']));
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
 function lcr58485b5e906a5p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr58485b5e906a5err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr58485b5e906a5m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr58485b5e906a5err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr58485b5e906a5raw($cx, $v) {
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
      $ret[] = lcr58485b5e906a5raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr58485b5e906a5m($cx, $a, $b) {
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
            'json_encode' => function($data, $options) {
        return json_encode($data);
    },
            'L' => function($term, $options) {
        return \Postleaf\Language::term($term, $options['hash']);
    },
            'markdown' => function($markdown, $options) {
        return \Postleaf\Postleaf::markdownToHtml($markdown);
    },
);
    $partials = array('header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('title'))),' &middot; ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</title>
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'    <meta name="apple-mobile-web-app-title" content="Postleaf">
',$sp,'    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1, minimal-ui">
',$sp,'    <meta name="postleaf:language" data-cancel="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'" data-ok="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('ok'),array()), 'encq', $in)),'" data-changes-saved="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('your_changes_have_been_saved'),array()), 'encq', $in)),'">
',$sp,'    <meta name="postleaf:template" content="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template'))),'">
',$sp,'    <meta name="postleaf:url" data-base="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" data-admin="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'admin_url', array(array(),array()), 'encq', $in)),'">
',$sp,'',lcr58485b5e906a5hbch($cx, 'either', array(array(lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')),'source/assets/img/logo-color.png'),array()), $in, false, function($cx, $in)use($sp){echo '    <link rel="apple-touch-icon" href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'    <link rel="shortcut icon" href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'';}),'    <link rel="stylesheet" href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array('source/assets/css/lib.css'),array()), 'encq', $in)),'?v=',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'">
',$sp,'    ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'admin_styles', array(array(),array()), 'encq', $in)),'
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,600,600italic">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono:300">
',$sp,'</head>
',$sp,'<body class="admin';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('body_class')), false)){echo ' ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('body_class'))),'';}else{echo '';}echo ' preload">';return ob_get_clean();},
'menu' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<nav class="main-menu">
',$sp,'    <a aria-label="Postleaf" href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" title="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'" data-toggle="tooltip">
',$sp,'        <img src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array('source/assets/img/1.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'    </a>
',$sp,'
',$sp,'',lcr58485b5e906a5hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr58485b5e906a5sec($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '            <a href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('link'))),'" title="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('title'))),'" data-toggle="tooltip" class="';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                    <img src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                    <i class="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '            </a>
',$sp,'';}),'';}),'</nav>
',$sp,'
',$sp,'<nav class="mobile-menu">
',$sp,'    <div class="mobile-menu-header">
',$sp,'        <a class="mobile-menu-logo" href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'blog_url', array(array(),array()), 'encq', $in)),'">
',$sp,'            <img src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array('source/assets/img/12.png'),array()), 'encq', $in)),'" alt="Logo">
',$sp,'        </a>
',$sp,'        <span class="mobile-menu-toggle" href="#">
',$sp,'            <i class="fa fa-navicon"></i>
',$sp,'        </span>
',$sp,'        <div class="mobile-menu-title">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('title'))),'</div>
',$sp,'    </div>
',$sp,'    <div class="mobile-menu-items">
',$sp,'',lcr58485b5e906a5hbch($cx, 'admin_menu', array(array(),array()), $in, false, function($cx, $in)use($sp){echo '',lcr58485b5e906a5sec($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('items')), null, $in, true, function($cx, $in)use($sp){echo '                <a href="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('link'))),'" class="';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('current')), false)){echo 'current ';}else{echo '';}echo '';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('class')), false)){echo '',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('class'))),'';}else{echo '';}echo '">
',$sp,'';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                        <img src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'" alt="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('avatar'),array()), 'encq', $in)),'"></i>
',$sp,'';}else{echo '                        <i class="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('icon'))),'"></i>
',$sp,'';}echo '                    <span class="description">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('title'))),'</span>
',$sp,'                </a>
',$sp,'';}),'';}),'    </div>
',$sp,'</nav>';return ob_get_clean();},
'history-table' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('history')), false)){echo '    <table class="table history-table">
',$sp,'',lcr58485b5e906a5sec($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('history')), null, $in, true, function($cx, $in)use($sp){echo '            <tr>
',$sp,'                <td>
',$sp,'                    ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'date', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('rev_date'))),array('format'=>'%d %b %Y @ %H:%M')), 'encq', $in)),'
',$sp,'                    <br>
',$sp,'                    <small class="text-muted">
',$sp,'',lcr58485b5e906a5hbch($cx, 'admin_author', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post_data','author'))),array()), $in, false, function($cx, $in)use($sp){echo '';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('avatar')), false)){echo '                                <img class="avatar" src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('avatar'))),array()), 'encq', $in)),'">
',$sp,'';}else{echo '';}echo '                            ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('name'))),'
',$sp,'';}, function($cx, $in)use($sp){echo '                            ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post_data','author'))),'
',$sp,'';}),'
',$sp,'';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('initial')), false)){echo '                            &middot; ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('initial_version'),array()), 'encq', $in)),'
',$sp,'';}else{echo '';}echo '                    </small>
',$sp,'                </td>
',$sp,'                <td>
',$sp,'                    <button
',$sp,'                        type="button"
',$sp,'                        class="btn btn-sm btn-secondary"
',$sp,'                        data-view-history="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('id'))),'"
',$sp,'                        data-url="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'admin_url', array(array('posts/'),array()), 'encq', $in)),'',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'encode', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post_data','slug'))),array()), 'encq', $in)),'/history/',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'encode', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('id'))),array()), 'encq', $in)),'"
',$sp,'                    >
',$sp,'                        <i class="fa fa-external-link"></i>
',$sp,'                    </a>
',$sp,'                </td>
',$sp,'                <td>
',$sp,'                    <button
',$sp,'                        type="button"
',$sp,'                        class="btn btn-sm btn-secondary"
',$sp,'                        data-delete-history="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('id'))),'"
',$sp,'                        data-confirm="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('delete_this_revision'),array()), 'encq', $in)),'"
',$sp,'                    >
',$sp,'                        <i class="fa fa-trash-o"></i>
',$sp,'                    </button>
',$sp,'                </td>
',$sp,'                <td>
',$sp,'                    <button
',$sp,'                        type="button"
',$sp,'                        class="btn btn-sm btn-warning"
',$sp,'                        data-restore-history="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('id'))),'"
',$sp,'                    >
',$sp,'                        <i class="fa fa-undo"></i>
',$sp,'                    </button>
',$sp,'                    <i class="loader loader-sm" hidden></i>
',$sp,'                </td>
',$sp,'            </tr>
',$sp,'';}),'    </table>
',$sp,'';}else{echo '    <div class="history-none valign">
',$sp,'        <div class="valign-middle">
',$sp,'            <i class="fa fa-clock-o"></i>
',$sp,'            ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('no_history'),array()), 'encq', $in)),'
',$sp,'        </div>
',$sp,'    </div>
',$sp,'';}echo '';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user')), false)){echo '<div class="locater-overlay" hidden></div>
',$sp,'<div class="locater" hidden>
',$sp,'    <div class="form-group">
',$sp,'        <div class="inner-addon-group">
',$sp,'            <span class="inner-addon"><i class="fa fa-search"></i></span>
',$sp,'            <input type="text" class="form-control locater-input" placeholder="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'">
',$sp,'        </div>
',$sp,'    </div>
',$sp,'    <div class="locater-results"></div>
',$sp,'</div>
',$sp,'';}else{echo '';}echo '
',$sp,'<script src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array('source/assets/js/lib.min.js'),array()), 'encq', $in)),'?v=',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'"></script>
',$sp,'',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'admin_scripts', array(array(),array()), 'encq', $in)),'
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
    
    ob_start();echo '',lcr58485b5e906a5p($cx, 'header', array(array($in),array()),0),'
',lcr58485b5e906a5p($cx, 'menu', array(array($in),array()),0),'
<div class="container-fluid">
    <div class="row">
        <div class="editor-toolbar" hidden>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary" data-editor="undo"><i class="fa fa-undo"></i></button>
                <button type="button" class="btn btn-secondary" data-editor="redo"><i class="fa fa-repeat"></i></button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-secondary" data-editor="bold"><i class="fa fa-bold"></i></button>
                <button type="button" class="btn btn-secondary" data-editor="italic"><i class="fa fa-italic"></i></button>
                <div class="dropdown-btn">
                    <span class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-font"></i>
                    </span>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" data-editor="underline">
                            <i class="fa fa-underline"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('underline'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="strikethrough">
                            <i class="fa fa-strikethrough"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('strikethrough'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="subscript">
                            <i class="fa fa-subscript"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('subscript'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="superscript">
                            <i class="fa fa-superscript"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('superscript'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="code">
                            <i class="fa fa-code"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('code'),array()), 'encq', $in)),'
                        </button>

                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" data-editor="removeFormat">
                            <i class="fa fa-eraser"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('clear_formatting'),array()), 'encq', $in)),'
                        </button>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <div class="dropdown-btn">
                    <span class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-paragraph"></i>
                    </span>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" data-editor="heading1">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('heading_1'),array()), 'encq', $in)),'</button>
                        <button class="dropdown-item" data-editor="heading2">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('heading_2'),array()), 'encq', $in)),'</button>
                        <button class="dropdown-item" data-editor="heading3">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('heading_3'),array()), 'encq', $in)),'</button>
                        <button class="dropdown-item" data-editor="heading4">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('heading_4'),array()), 'encq', $in)),'</button>
                        <button class="dropdown-item" data-editor="heading5">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('heading_5'),array()), 'encq', $in)),'</button>
                        <button class="dropdown-item" data-editor="heading6">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('heading_6'),array()), 'encq', $in)),'</button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" data-editor="paragraph">
                            <i class="fa fa-paragraph"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('paragraph'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="preformatted">
                            <i class="fa fa-keyboard-o"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('preformatted'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="blockquote">
                            <i class="fa fa-quote-right"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('blockquote'),array()), 'encq', $in)),'
                        </button>
                    </div>
                </div>

                <div class="dropdown-btn">
                    <span class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-align-left"></i>
                    </span>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" data-editor="alignLeft">
                            <i class="fa fa-align-left"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('align_left'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="alignCenter">
                            <i class="fa fa-align-center"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('align_center'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="alignRight">
                            <i class="fa fa-align-right"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('align_right'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="alignJustify">
                            <i class="fa fa-align-justify"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('justify'),array()), 'encq', $in)),'
                        </button>
                    </div>
                </div>

                <div class="dropdown-btn">
                    <span class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-list-ul"></i>
                    </span>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" data-editor="unorderedList">
                            <i class="fa fa-list-ul"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('bulleted_list'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="orderedList">
                            <i class="fa fa-list-ol"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('numbered_list'),array()), 'encq', $in)),'
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" data-editor="indent">
                            <i class="fa fa-indent"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('increase_indent'),array()), 'encq', $in)),'
                        </button>
                        <button class="dropdown-item" data-editor="outdent">
                            <i class="fa fa-outdent"></i>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('decrease_indent'),array()), 'encq', $in)),'
                        </button>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-secondary" data-editor="link"><i class="fa fa-link"></i></button>
                <button type="button" class="btn btn-secondary" data-editor="image"><i class="fa fa-picture-o"></i></button>
                <button type="button" class="btn btn-secondary" data-editor="embed"><i class="fa fa-cube"></i></button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-secondary',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('cookies','zen')),'true'),array()), $in, false, function($cx, $in) {echo ' active';}),'" data-editor="zen"><i class="fa fa-leaf"></i></button>
                <button type="button" class="btn btn-secondary" data-editor="settings"><i class="fa fa-cog"></i></button>
            </div>

            <div class="btn-group">
                <button type="button" class="btn ',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','status')),'draft'),array()), $in, false, function($cx, $in) {echo 'btn-warning';}, function($cx, $in) {echo 'btn-primary';}),'" data-editor="save">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('save'),array()), 'encq', $in)),'</button>
            </div>
        </div>
    </div>
</div>

<div class="editor stretch-down">
    <div class="editor-loader valign" hidden>
        <div class="valign-middle text-xs-center">
            <i class="loader loader-xl"></i>
        </div>
    </div>

    <iframe
        class="editor-frame"
        data-src="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('frame_src'))),'"
        data-default-title="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','default_title'))),'"
        data-default-content="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'markdown', array(array(lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','default_content'))),array()), 'encq', $in)),'"
        data-unsaved-changes="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('your_changes_have_not_been_saved_yet'),array()), 'encq', $in)),'"
        hidden
    ></iframe>
</div>

<div class="dropzone" hidden>
    <div class="dropzone-target valign" data-target="post-image">
        <div class="valign-middle">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('set_post_image'),array()), 'encq', $in)),'</div>
    </div>
    <div class="dropzone-target valign" data-target="content">
        <div class="valign-middle">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('insert_into_post'),array()), 'encq', $in)),'</div>
    </div>
</div>

<div class="link-panel panel panel-right">
    <form class="link-form" autocomplete="off" novalidate>
        <button type="button" class="close btn btn-link" data-panel="hide"><i class="fa fa-remove"></i></button>
        <h3>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('link'),array()), 'encq', $in)),'</h3>
        <div class="form-group">
            <label for="link-href">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('url'),array()), 'encq', $in)),'</label>
            <div class="inner-addon-group">
                <span class="inner-addon"><i class="fa fa-link"></i></span>
                <input class="form-control" type="url" name="href" id="link-href">
                <label class="inner-addon upload-file">
                    <i class="fa fa-upload"></i>
                    <input type="file" style="display: none;">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="link-title">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('title'),array()), 'encq', $in)),'</label>
            <input class="form-control" type="text" name="title" id="link-title">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="new-window" id="link-new-window">
                ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('open_in_a_new_window'),array()), 'encq', $in)),'
            </label>
        </div>
        <div class="form-group m-t-2">
            <button class="btn btn-secondary unlink pull-right" type="button"><i class="fa fa-unlink"></i></button>
            <button class="btn btn-primary" type="submit">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('submit'),array()), 'encq', $in)),'</button>
            <button class="btn btn-secondary" type="button" data-panel="hide">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'</button>
        </div>
    </form>
</div>

<div class="image-panel panel panel-right">
    <form class="image-form" autocomplete="off" novalidate>
        <button type="button" class="close btn btn-link" data-panel="hide"><i class="fa fa-remove"></i></button>
        <h3>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('image'),array()), 'encq', $in)),'</h3>
        <div class="form-group">
            <label for="image-src">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('source'),array()), 'encq', $in)),'</label>
            <div class="inner-addon-group">
                <span class="inner-addon"><i class="fa fa-picture-o"></i></span>
                <input class="form-control" type="url" name="src" id="image-src">
                <label class="inner-addon upload-image">
                    <i class="fa fa-camera"></i>
                    <input type="file" accept="image/*" style="display: none;">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="image-alt">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('description'),array()), 'encq', $in)),'</label>
            <input class="form-control" type="text" name="alt" id="image-alt">
        </div>
        <div class="form-group">
            <label for="image-href">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('url'),array()), 'encq', $in)),'</label>
            <div class="inner-addon-group">
                <span class="inner-addon"><i class="fa fa-link"></i></span>
                <input class="form-control" type="url" name="href" id="image-href">
            </div>
        </div>
        <div class="form-group">
            <label>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('alignment'),array()), 'encq', $in)),'</label>
            <div class="radio">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-secondary active">
                        <input type="radio" class="image-align-none" name="align" value="none">
                        <i class="fa fa-ban"></i>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" class="image-align-left" name="align" value="left">
                        <i class="fa fa-align-left"></i>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" class="image-align-center" name="align" value="center">
                        <i class="fa fa-align-center"></i>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" class="image-align-right" name="align" value="right">
                        <i class="fa fa-align-right"></i>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="image-width">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('width'),array()), 'encq', $in)),'</label>
                    <input class="form-control" type="number" name="width" id="image-width" min="0">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="image-height">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('height'),array()), 'encq', $in)),'</label>
                    <input class="form-control" type="number" name="height" id="image-height" min="0">
                </div>
            </div>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="constrain" id="image-constrain" checked>
                ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('constrain_proportions'),array()), 'encq', $in)),'
            </label>
        </div>
        <div class="form-group m-t-2">
            <button class="btn btn-secondary delete-image pull-right" type="button"><i class="fa fa-trash-o"></i></button>
            <button class="btn btn-primary" type="submit">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('submit'),array()), 'encq', $in)),'</button>
            <button class="btn btn-secondary" type="button" data-panel="hide">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'</button>
        </div>
    </form>
</div>

<div class="embed-panel panel panel-right">
    <form class="embed-form" autocomplete="off">
        <button type="button" class="close btn btn-link" data-panel="hide"><i class="fa fa-remove"></i></button>
        <h3>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('embed'),array()), 'encq', $in)),'</h3>
        <div class="form-group">
            <label for="embed-code">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('code'),array()), 'encq', $in)),'</label>
            <div class="tag-cover">
                <textarea class="form-control code" name="code" id="embed-code" rows="6" spellcheck="false"></textarea>
                <span class="tag tag-default tag-tr tag-outside">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('html'),array()), 'encq', $in)),'</span>
            </div>
            <small class="text-muted">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('paste_embed_code_or_url_here'),array()), 'encq', $in)),'</small>
        </div>
        <div class="form-group">
            <label>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('alignment'),array()), 'encq', $in)),'</label>
            <div class="radio">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-secondary active">
                        <input type="radio" class="embed-align-none" name="align" value="none">
                        <i class="fa fa-ban"></i>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" class="embed-align-left" name="align" value="left">
                        <i class="fa fa-align-left"></i>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" class="embed-align-center" name="align" value="center">
                        <i class="fa fa-align-center"></i>
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" class="embed-align-right" name="align" value="right">
                        <i class="fa fa-align-right"></i>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group m-t-2">
            <button class="btn btn-secondary delete-embed pull-right" type="button"><i class="fa fa-trash-o"></i></button>
            <button class="btn btn-primary" type="submit">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('submit'),array()), 'encq', $in)),'</button>
            <button class="btn btn-secondary" type="button" data-panel="hide">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'</button>
        </div>
    </form>
</div>

<div class="settings-panel panel panel-right">
    <form class="settings-form" autocomplete="off">
        <button type="button" class="close btn btn-link" data-panel="hide"><i class="fa fa-remove"></i></button>
        <h3>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('post'),array()), 'encq', $in)),'</h3>

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#settings" role="tab" data-toggle="tab">
                    <i class="fa fa-file-text-o hidden-sm-up"></i>
                    <span class="hidden-xs-down">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('settings'),array()), 'encq', $in)),'</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#meta" role="tab" data-toggle="tab">
                    <i class="fa fa-info hidden-sm-up"></i>
                    <span class="hidden-xs-down">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('metadata'),array()), 'encq', $in)),'</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#history" role="tab" data-toggle="tab">
                    <i class="fa fa-clock-o hidden-sm-up"></i>
                    <span class="hidden-xs-down">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('history'),array()), 'encq', $in)),'</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="settings" role="tabpanel">
                <div class="form-group">
                    <label for="slug">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('slug'),array()), 'encq', $in)),'</label>
                    <div class="inner-addon-group">
                        <span class="inner-addon"><i class="fa fa-link"></i></span>
                        <input class="form-control" type="text" name="slug" id="slug" value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','slug'))),'">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="pub-date">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('date'),array()), 'encq', $in)),'</label>
                            <div class="inner-addon-group">
                                <span class="inner-addon"><i class="fa fa-calendar-o"></i></span>
                                <input class="form-control" type="text" name="pub-date" id="pub-date" value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'date', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','pub_date'))),array('format'=>'%d %b %Y')), 'encq', $in)),'">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="pub-time">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('time'),array()), 'encq', $in)),'</label>
                            <div class="inner-addon-group">
                                <span class="inner-addon"><i class="fa fa-clock-o"></i></span>
                                <input class="form-control" type="text" name="pub-time" id="pub-time" value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'date', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','pub_date'))),array('format'=>'%H:%M')), 'encq', $in)),'">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="image">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('image'),array()), 'encq', $in)),'</label>
                    <input type="hidden" name="image" id="image" value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','image'))),'">
                    <div class="post-image card"';if (lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','image')), false)){echo ' style="background-image: url(\'',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'url', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','image'))),array()), 'encq', $in)),'\');"';}else{echo '';}echo '>
                        <div class="controls">
                            <label class="upload-post-image btn btn-translucent-dark">
                                <i class="fa fa-fw fa-camera"></i>
                                <input type="file" accept="image/*" style="display: none;">
                            </label>
                            <button type="button" class="remove-post-image btn btn-translucent-dark"';if (!lcr58485b5e906a5ifvar($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','image')), false)){echo ' hidden';}else{echo '';}echo '>
                                <i class="fa fa-fw fa-remove"></i>
                            </button>
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <label for="tags">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('tags'),array()), 'encq', $in)),'</label>
                    <input class="form-control" type="text" name="tags" id="tags"
                        data-all-tags="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'json_encode', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('all_tags'))),array()), 'encq', $in)),'"
                        data-post-tags="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'json_encode', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post_tags'))),array()), 'encq', $in)),'"
                        data-can-create-tags="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('can_create_tags'))),'"
                    >
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="author">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('author'),array()), 'encq', $in)),'</label>
                        <select class="form-control" name="author" id="author">
',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','role')),'in','owner,admin,editor'),array()), $in, false, function($cx, $in) {echo '',lcr58485b5e906a5sec($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('authors')), null, $in, true, function($cx, $in) {echo '                            <option value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('slug'))),'"
',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template')),'posts.new'),array()), $in, false, function($cx, $in) {echo '                                    ',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('slug')),lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','slug'))),array()), $in, false, function($cx, $in) {echo 'selected';}),'
';}, function($cx, $in) {echo '                                    ',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('slug')),lcr58485b5e906a5v($cx, $in, isset($cx['scopes'][count($cx['scopes'])-1]) ? $cx['scopes'][count($cx['scopes'])-1] : null, array('post','author'))),array()), $in, false, function($cx, $in) {echo 'selected';}),'
';}),'                            >',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('name'))),'</option>
';}),'';}, function($cx, $in) {echo '                            <option value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','slug'))),'" selected>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','name'))),'</option>
';}),'                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="status">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('status'),array()), 'encq', $in)),'</label>
                        <select class="form-control" name="status" id="status">
                            <option value="published"',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','status')),'published'),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('published'),array()), 'encq', $in)),'</option>
                            <option value="draft"',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','status')),'draft'),array()), $in, false, function($cx, $in) {echo ' selected';}),'>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('draft'),array()), 'encq', $in)),'</option>
                        </select>
                    </div>
                </div>
',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user','role')),'in','owner,admin,editor'),array()), $in, false, function($cx, $in) {echo '                <div class="checkbox m-t-2">
                    <label>
                        <input type="checkbox" name="featured" id="featured" value="on"',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','featured'))),array()), $in, false, function($cx, $in) {echo ' checked';}),'>
                        <span class="tag tag-success">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('feature_this_post'),array()), 'encq', $in)),'</span>
                    </label>
                </div>
                <div class="checkbox m-t-1">
                    <label>
                        <input type="checkbox" name="sticky" id="sticky" value="on"',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','sticky'))),array()), $in, false, function($cx, $in) {echo ' checked';}),'>
                        <span class="tag tag-info">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('make_this_post_sticky'),array()), 'encq', $in)),'</span>
                    </label>
                </div>
                <div class="checkbox m-t-1">
                    <label>
                        <input type="checkbox" name="page" id="page" value="on"',lcr58485b5e906a5hbch($cx, 'is', array(array(lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','page'))),array()), $in, false, function($cx, $in) {echo ' checked';}),'>
                        <span class="tag tag-primary">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('turn_this_post_into_a_page'),array()), 'encq', $in)),'</span>
                    </label>
                </div>
';}),'            </div>

            <div class="tab-pane" id="meta" role="tabpanel">
                <div class="form-group">
                    <label for="meta-title">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('meta_title'),array()), 'encq', $in)),'</label>
                    <input class="form-control" type="text" name="meta-title" id="meta-title" value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','meta_title'))),'" autofocus>
                </div>
                <div class="form-group">
                    <label for="meta-description">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('meta_description'),array()), 'encq', $in)),'</label>
                    <textarea class="form-control" name="meta-description" id="meta-description" rows="4">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','meta_description'))),'</textarea>
                </div>
                <div class="form-group">
                    <label>',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'L', array(array('search_engine_preview'),array()), 'encq', $in)),'</label>
                    <div class="se-preview">
                        <div class="se-title"></div>
                        <div class="se-url">
                            ',lcr58485b5e906a5encq($cx, lcr58485b5e906a5hbch($cx, 'post_url', array(array('/'),array()), 'encq', $in)),'<span class="se-slug">',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','slug'))),'</span>
                        </div>
                        <div class="se-description"></div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="history" role="tabpanel">
                <div class="history">
',lcr58485b5e906a5p($cx, 'history-table', array(array($in),array()),0, '                    '),'                </div>
            </div>
        </div>

        <input type="hidden" name="post" value="',lcr58485b5e906a5encq($cx, lcr58485b5e906a5v($cx, $in, isset($in) ? $in : null, array('post','slug'))),'">
        <button type="submit" hidden></button>
    </form>
</div>

',lcr58485b5e906a5p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};