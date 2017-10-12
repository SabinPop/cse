<?php  function lcr57fdc5a69ffa8v($cx, $in, $base, $path, $args = null) {
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
   lcr57fdc5a69ffa8err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
  }
 }
 function lcr57fdc5a69ffa8encq($cx, $var) {
  if ($var instanceof LS) {
   return (string)$var;
  }

  return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlentities(lcr57fdc5a69ffa8raw($cx, $var), ENT_QUOTES, 'UTF-8'));
 }
 function lcr57fdc5a69ffa8hbch($cx, $ch, $vars, $op, $inverted, $cb = null, $else = null) {
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
     $ret = $cb($cx, is_array($ex) ? lcr57fdc5a69ffa8m($cx, $op, $ex) : $op);
    } else {
     $cx['scopes'][] = $op;
     $ret = $cb($cx, is_array($ex) ? lcr57fdc5a69ffa8m($cx, $context, $ex) : $context);
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
   lcr57fdc5a69ffa8err($cx, $e);
  }

  return $r;
 }
 function lcr57fdc5a69ffa8ifvar($cx, $v, $zero) {
  return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
 }
 function lcr57fdc5a69ffa8p($cx, $p, $v, $pid, $sp = '') {
  if ($p === '@partial-block') {
   $p = "$p" . ($pid > 0 ? $pid : $cx['partialid']);
  }

  if (!isset($cx['partials'][$p])) {
   lcr57fdc5a69ffa8err($cx, "Can not find partial named as '$p' !!");
   return '';
  }

  $cx['partialid'] = $pid;

  return call_user_func($cx['partials'][$p], $cx, lcr57fdc5a69ffa8m($cx, $v[0][0], $v[1]), $sp);
 }
 function lcr57fdc5a69ffa8err($cx, $err) {
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
   error_log($err);
   return;
  }
  if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
   throw new \Exception($err);
  }
 }
 function lcr57fdc5a69ffa8raw($cx, $v) {
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
      $ret[] = lcr57fdc5a69ffa8raw($cx, $vv);
     }
     return join(',', $ret);
    }
   } else {
    return 'Array';
   }
  }

  return "$v";
 }
 function lcr57fdc5a69ffa8m($cx, $a, $b) {
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
    $helpers = array(            'admin_scripts' => function() {
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
            'L' => function($term, $options) {
        return \Postleaf\Language::term($term, $options['hash']);
    },
);
    $partials = array('header' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'<!DOCTYPE html>
',$sp,'<html>
',$sp,'<head>
',$sp,'    <title>',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($in) ? $in : null, array('title'))),' &middot; ',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</title>
',$sp,'    <meta charset="utf-8">
',$sp,'    <meta http-equiv="X-UA-Compatible" content="IE=edge">
',$sp,'    <meta name="apple-mobile-web-app-title" content="Postleaf">
',$sp,'    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, maximum-scale=1, minimal-ui">
',$sp,'    <meta name="postleaf:language" data-cancel="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('cancel'),array()), 'encq', $in)),'" data-ok="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('ok'),array()), 'encq', $in)),'" data-changes-saved="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('your_changes_have_been_saved'),array()), 'encq', $in)),'">
',$sp,'    <meta name="postleaf:template" content="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('template'))),'">
',$sp,'    <meta name="postleaf:url" data-base="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array(),array()), 'encq', $in)),'" data-admin="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'admin_url', array(array(),array()), 'encq', $in)),'">
',$sp,'',lcr57fdc5a69ffa8hbch($cx, 'either', array(array(lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','favicon')),'source/assets/img/logo-color.png'),array()), $in, false, function($cx, $in)use($sp){echo '    <link rel="apple-touch-icon" href="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'    <link rel="shortcut icon" href="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array($in),array()), 'encq', $in)),'">
',$sp,'';}),'    <link rel="stylesheet" href="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array('source/assets/css/lib.css'),array()), 'encq', $in)),'?v=',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'">
',$sp,'    ',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'admin_styles', array(array(),array()), 'encq', $in)),'
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,600,600italic">
',$sp,'    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono:300">
',$sp,'</head>
',$sp,'<body class="admin';if (lcr57fdc5a69ffa8ifvar($cx, lcr57fdc5a69ffa8v($cx, $in, isset($in) ? $in : null, array('body_class')), false)){echo ' ',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($in) ? $in : null, array('body_class'))),'';}else{echo '';}echo ' preload">';return ob_get_clean();},
'footer' => function ($cx, $in, $sp) {ob_start();echo '',$sp,'';if (lcr57fdc5a69ffa8ifvar($cx, lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('user')), false)){echo '<div class="locater-overlay" hidden></div>
',$sp,'<div class="locater" hidden>
',$sp,'    <div class="form-group">
',$sp,'        <div class="inner-addon-group">
',$sp,'            <span class="inner-addon"><i class="fa fa-search"></i></span>
',$sp,'            <input type="text" class="form-control locater-input" placeholder="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('search'),array()), 'encq', $in)),'">
',$sp,'        </div>
',$sp,'    </div>
',$sp,'    <div class="locater-results"></div>
',$sp,'</div>
',$sp,'';}else{echo '';}echo '
',$sp,'<script src="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array('source/assets/js/lib.min.js'),array()), 'encq', $in)),'?v=',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('postleaf','version'))),'"></script>
',$sp,'',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'admin_scripts', array(array(),array()), 'encq', $in)),'
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
    
    ob_start();echo '',lcr57fdc5a69ffa8p($cx, 'header', array(array($in),array()),0),'
<form class="login-form" autocomplete="off" data-redirect="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($in) ? $in : null, array('redirect'))),'">
    <div class="text-xs-center m-b-2">
        <a href="http://cseunirea.ro/" target="_blank">
            <img class="logo" width="650px" src="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array('source/assets/img/12.png'),array()), 'encq', $in)),'">
        </a>
    </div>

    <div class="form-group">
        <label for="username">',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('username'),array()), 'encq', $in)),'</label>
        <input type="text" name="username" id="username" class="form-control" autofocus>
    </div>

    <div class="form-group">
        <label for="password">',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('password'),array()), 'encq', $in)),'</label>
        <div class="inner-addon-group">
            <input type="password" name="password" id="password" class="form-control">
            <a href="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'admin_url', array(array('login/recover'),array()), 'encq', $in)),'" class="inner-addon"
                title="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('lost_your_password'),array()), 'encq', $in)),'"
                data-toggle="tooltip"
                data-placement="top"
            >
                <i class="fa fa-question-circle"></i>
            </a>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-primary btn-block" type="submit">
            ',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'L', array(array('login'),array()), 'encq', $in)),'
        </button>
    </div>

    <div class="text-xs-center m-t-2">
        <a href="',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8hbch($cx, 'url', array(array(),array()), 'encq', $in)),'">',lcr57fdc5a69ffa8encq($cx, lcr57fdc5a69ffa8v($cx, $in, isset($cx['sp_vars']) ? $cx['sp_vars'] : null, array('settings','title'))),'</a>
    </div>

    <div class="form-message text-xs-center m-t-2" hidden></div>
</form>

',lcr57fdc5a69ffa8p($cx, 'footer', array(array($in),array()),0),'';return ob_get_clean();
};