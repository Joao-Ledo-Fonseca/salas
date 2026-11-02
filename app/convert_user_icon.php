<?php
// Converter SVG utilizador para viewBox 0 0 24 24 em outline
// viewBox original: 796 796 200 200  =>  subtrair 796, dividir por 200/24
$scale = 24 / 200;
$off = 796;

function n($v) { global $scale, $off; return round((float)$v * $scale, 3); }
function no($v) { global $scale, $off; return round(((float)$v - $off) * $scale, 3); }

// Path 1: M inicial absoluto, resto c relativos (c minúsculo = relativo, só ajusta M)
// M987.675,969.676  =>  M191.675,173.676 => *scale
$p1 = 'M987.675,969.676c-6.521-22.754-21.304-41.99-40.878-54.33c-3.862-2.435-8.873-2.039-12.299,0.979c-10.504,9.251-23.506,14.76-37.618,14.76c-14.391,0-27.624-5.723-38.236-15.304c-3.388-3.058-8.374-3.519-12.269-1.14c-20.15,12.303-35.401,31.837-42.052,55.035c-1.787,6.223-0.531,12.933,3.364,18.109c3.905,5.169,10.005,8.215,16.48,8.215h143.661c6.48,0,12.582-3.034,16.486-8.215z';
$p1 = preg_replace('/^M([\d.-]+),([\d.-]+)/', 'M ' . no('$1') . ',' . no('$2'), $p1);
// c relativos: dividir valores por 200/24
$p1 = preg_replace_callback('/c([\d.-]+),([\d.-]+)-([\d.-]+),([\d.-]+)-([\d.-]+),([\d.-]+)/', function($m){
    return 'c ' . n($m[1]) . ',' . n($m[2]) . ' ' . n($m[3]) . ',' . n($m[4]) . ' ' . n($m[5]) . ',' . n($m[6]);
}, $p1);
// h relativo
$p1 = preg_replace('/h([\d.-]+)/', 'h ' . n('$1'), $p1);
// Último segmento: c6.48,0,12.582-3.034,16.486-8.215z (já coberto acima? não, tem vírgula diferente)
// Vamos fazer replace genérico para todos os números depois de c/h
// Na verdade, vamos simplesmente dividir todos os números isolados por vírgula ou espaço
$p1 = preg_replace_callback('/([\d.-]+)/', function($m){
    return n($m[1]);
}, $p1);
// O problema: n() subtrai offset também, mas os relativos não devem subtrair offset!
// Vamos refazer de forma correta abaixo.

echo "A ver logica...\n";
?>