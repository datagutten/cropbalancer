<?Php
function colorcheck($im,$x,$y,$limit)
{
	$color=imagecolorat($im,$x,$y);		
	if($color<$limit)
		$return=array($x,$y);
	else
		$return=false;
	return $return;
}


if(!isset($im))
	$im=imagecreatefromjpeg('balance.jpg');
if(!is_resource($im))
	die("Invalid image\n");

if(!isset($argv[1]))
	require 'config_400dpi.php';
elseif(file_exists("config_{$argv[1]}.php"))
	require "config_{$argv[1]}.php";
else
	die("No config file found\n");
require 'tools/color.php';
$colortools=new color;

$width=imagesx($im);
$height=imagesy($im);


$max_y=$height-1;
$max_x=$width-1; //imagesx returns the width. The last position is one less (counted from 0)

$sides=array("Left","Right","Top","Bottom");

$list_loop=array("Left"=>range(0,$xlimit),"Right"=>range($max_x,$max_x-$xlimit),"Top"=>range(0,$ylimit),"Bottom"=>range($max_y,$max_y-$ylimit));
$list_common=array("Left"=>$vertical_positions,"Right"=>$vertical_positions,"Top"=>$horizontal_positions,"Bottom"=>$horizontal_positions);

foreach($sides as $side)
{
	echo $side."\n";
	foreach($list_common[$side] as $common)
	{
		foreach($list_loop[$side] as $loop)
		{
			if($side=="Top" || $side=="Bottom")
			{
				$pos="X: $common Y: $loop\n";
				$check=$colortools->colordiff($borderlimit,$color=imagecolorat($im,$common,$loop),$limit_low,$limit_high); //For top and bottom loop y axis
			}
			else
			{
				$pos="X: $loop Y: $common\n";
				$check=$colortools->colordiff($borderlimit,$color=imagecolorat($im,$loop,$common),$limit_low,$limit_high); //For left and right loop x axis
			}
			if(isset($debug))
			{
				echo $pos;
				echo dechex($color)."\n";
				print_r($colortools->diff);
			}
			if($check===true)
			{
				$commons[$side]=$common;
				$line[$side]=$loop;
				break 2;
			}
		}
	}
}

//Some variable renaming to keep old code working
$from['Right']=$max_x-$line['Right'];
$from['Bottom']=$max_y-$line['Bottom'];

$fromleft=$from['Left'];
$fromright=$from['Right'];
$fromtop=$from['Top'];
$frombottom=$from['Bottom'];


echo "Line positions:\n";
echo "From left: $fromleft ($lefty)\n";
echo "From right: $fromright ($righty)\n";
echo "From top: $fromtop ($topx)\n";
echo "From bottom: $frombottom ($bottomx)\n\n";

if($fromleft>$fromright)
{
	echo "Crop ";
	echo $diff_tb_lr=$fromleft-$fromright;
	echo " pixels on the left side\n";
	$leftnew=$fromleft-$diff_tb_lr;
	echo "The new line position from left:$leftnew\n";
}
elseif($fromleft<$fromright)
{
	echo "Crop ";
	echo $diff_tb_lr=$fromright-$fromleft;
	echo " pixels on the right side\n";
}
else
	echo "Both sides seem to be equal\n";

//Top and bottom
if($fromtop>$frombottom)
{
	echo "Crop ";
	echo $diff_tb=$fromtop-$frombottom;
	echo " pixels on the top\n";
}
elseif($fromtop<$frombottom)
{
	echo "Crop ";
	echo $diff_tb=$frombottom-$fromtop;
	echo " pixels on the bottom\n";
}
else
	echo "Top and bottom seems to be equal\n";

if(isset($diff_tb_lr))
{
	$balanced_width=$max_x+1-$diff_tb_lr;
	echo "The new width should be $balanced_width\n";
}
if(isset($diff_tb))
{
	$balanced_height=$max_y+1-$diff_tb;
	echo "The new height should be $balanced_height\n";
}


$normalize_left=$fromleft-$space_lr;
$normalize_right=$fromright-$space_lr;
$normalize_top=$fromtop-$space_tb;
$normalize_bottom=$frombottom-$space_tb;
if($normalize_left==-1)
	$normalize_left=0;

echo "\nDo this to get $space_lr pixels space:\n";
echo "Crop $normalize_left pixels on the left side\n";
echo "Crop $normalize_right pixels on the right side\n";
echo "\nDo this to get $space_tb on top and bottom:\n";
echo "Crop $normalize_top pixels on the top\n";
echo "Crop $normalize_bottom pixels on the bottom\n";
//echo "The height is ";
//echo imagesy($im)+1;
echo "\n";
