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

require 'config.php';

$width=imagesx($im);
$height=imagesy($im);


$max_y=$height-1;
$max_x=$width-1; //imagesx returns the width. The last position is one less (counted from 0)



foreach ($ylist as $y)
{
	if(!isset($fromleft)) //Check if line position from the left has been found on another y position
	{
		for ($x=5; $x<=$xlimit; $x++)	
		{
			colorcheck($im,$x,$y,$borderlimit,$pagelimit);
			$color=imagecolorat($im,$x,$y);
			/*echo $x.': '.dechex($color);
			echo "\n";*/
			/*if($color<$pagelimit && !isset($pageborder_left))
				$pageborder_left=$x;*/
			if($color<$borderlimit)
			{
				$fromleft=$x;
				$lefty=$y;
				break;
			}
		}
		var_dump($color."<".$borderlimit);
	}
	if(!isset($fromright))
	{
		for($x=$max_x-1; $x>=$max_x-700; $x--)
		{
			$color=imagecolorat($im,$x,$y);
			//echo $x.': '.dechex($color);
			//echo "\n";
			if($color<$borderlimit)
			{
				$fromright=$max_x-$x;
				$righty=$y;
				break;
			}
		}
	}
}


require 'cropper_topbottom.php';
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
