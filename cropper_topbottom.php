<?Php


foreach ($xlist as $x)
{
	if(!isset($fromtop))
	{
		for($y=0; $y<=$ylimit; $y++) //Search from the top
		{
				$color=imagecolorat($im,$x,$y);
				//echo $y.': '.dechex($color);
				//echo "\n";
				if($color<$borderlimit)
				{
					$fromtop=$y;
					$topx=$x;
					break;
				}
		}
	}
	if(!isset($frombottom))
	{
	for($y=$max_y-6; $y>=$max_y-1000; $y--) //Search from the bottom
	{
			$color=imagecolorat($im,$x,$y);
			//echo $y.': '.dechex($color);
			//echo " ($x)\n";
			if($color<$borderlimit)
			{
				$frombottom=$max_y-$y;
				$bottomx=$x;
				break;
			}
	}
	}
}
if(!isset($frombottom) || !isset($fromtop))
	die("error top/bottom\n");