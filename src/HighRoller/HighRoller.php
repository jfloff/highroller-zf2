<?php

/*
* HighRoller -- PHP wrapper for the popular JS charting library Highcharts
* Author:       jmaclabs@gmail.com
* Contributor:	jfloff@gmail.com
* File:         HighRoller.php
* Date:         Mon Aug 13 14:23:49 WEST 2012
* Version:      1.0.0
*
* Licensed to Gravity.com under one or more contributor license agreements.
* See the NOTICE file distributed with this work for additional information
* regarding copyright ownership.  Gravity.com licenses this file to you use
* under the Apache License, Version 2.0 (the License); you may not this
* file except in compliance with the License.  You may obtain a copy of the
* License at
*
*    http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an AS IS BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*
*/
?><?php
/**
* Author: jmaclabs
* Date: 9/14/11
* Time: 5:46 PM
* Desc: HighRoller Parent Class
*
* Licensed to Gravity.com under one or more contributor license agreements.
* See the NOTICE file distributed with this work for additional information
* regarding copyright ownership.  Gravity.com licenses this file to you use
* under the Apache License, Version 2.0 (the License); you may not this
* file except in compliance with the License.  You may obtain a copy of the
* License at
*
*    http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an AS IS BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*
*/

class HighRoller {

	public $chart;
	public $title;
	public $legend;
	public $tooltip;
	public $plotOptions;
	public $series = array();

	function __construct(){
		$this->chart = new HighRollerChart();
		$this->title = new HighRollerTitle();
		$this->legend = new HighRollerLegend();
		$this->tooltip = new HighRollerToolTip();
		$this->series = new HighRollerSeries();
	}

	function initPlotOptions(){
		$this->plotOptions = new HighRollerPlotOptions($this->chart->type);
		//$this->plotOptions->column = new HighRollerPlotOptionsByChartType($this->chart->type);
	}

	/** returns a javascript script tag with path to your HighCharts library source
	 * @static
	 * @param $location - path to your highcharts JS
	 * @return string - html script tag markup with your source location
	 */
	public static function setHighChartsLocation($location){
		return $scriptTag = "<!-- High Roller - High Charts Location-->
			<script type='text/javascript' src='" . $location . "'></script>";
	}

	/** returns a javascript script tag with path to your HighCharts library THEME source
	 * @static
	 * @param $location - path to your highcharts theme file
	 * @return string - html script tag markup with your source location
	 */
	public static function setHighChartsThemeLocation($location){
		return $scriptTag = "<!-- High Roller - High Charts Theme Location-->
		<script type='text/javascript' src='" . $location . "'></script>";
	}

	/** returns chart object with newly set obj property name
	 * @param $objName - string, name of the HighRoller Object you're operating on
	 * @param $propertyName - string, name of the property you want to set, can be a new property name
	 * @param $value - mixed, value you wish to assign to the property
	 * @return HighRoller
	 */
	public function setProperty($objName, $propertyName, $value){
		$this->$objName->$propertyName = $value;
		return $this;
	}

	/** add data to plot in your chart
	 * @param $chartdata - array, data provided in 1 of 3 HighCharts supported array formats (array, assoc array or mult-dimensional array)
	 * @return void
	 */
	public function addData($chartdata){
		if(!is_array($chartdata)){
			die("HighRoller::addData() - data format must be an array.");
		}
		$this->series = array($chartdata);
	}

	/** add series to your chart
	 * @param $chartdata - array, data provided in 1 of 3 HighCharts supported array formats (array, assoc array or mult-dimensional array)
	 * @return void
	 */
	public function addSeries(HighRollerSeriesData $chartData){
		if(is_object($this->series)){     // if series is an object
			$this->series = array($chartData);
		} else if(is_array($this->series)) {
			$this->series[] = $chartData;
		}
	}

	/** enable auto-step calc for xAxis labels for very large data sets.
	* @return void
	*/
	public function enableAutoStep(){
		if(is_array($this->series)) {
			$count = count($this->series[0]->data);
			$step = number_format(sqrt($count));
			if($count > 1000){
				$step = number_format(sqrt($count/$step));
			}
			$this->xAxis->labels->step = $step;
		}
	}

	/** returns new Highcharts javascript
	* @return string - highcharts!
	*/
	function renderChart($engine = 'jquery'){
		$options = new HighRollerOptions();   // change file/class name to new HighRollerGlobalOptions()

		if ( $engine == 'mootools')
			$chartJS = 'window.addEvent(\'domready\', function() {';
		else
			$chartJS = '$(document).ready(function() {';

		$chartJS .= "\n\n    // HIGHROLLER - HIGHCHARTS UTC OPTIONS ";

		$chartJS .= "\n    Highcharts.setOptions(\n";
		$chartJS .= "       " . json_encode($options) . "\n";
		$chartJS .= "    );\n";
		$chartJS .= "\n\n    // HIGHROLLER - HIGHCHARTS '" . $this->title->text . "' " . $this->chart->type . " chart";
		//options var
		$chartJS .= "\n    var ". $this->chart->renderTo . "ChartOptions = " . $this->getChartOptionsObject() .";";

		//formatters
		//$chartJS .= "\n    " . $this->chart->renderTo . "ChartOptions.tooltip.formatter = function() { return '<b> OLAA </b>'; };"; //example
		if ($this->tooltip->formatter != ""){
			$chartJS .= "\n    " . $this->chart->renderTo . "ChartOptions.tooltip.formatter = function() { " . $this->tooltip->formatter . " };"; //example
		}

		// too generalize
		if ($this->chart->type == 'pie'){
			if ($this->plotOptions->pie->dataLabels->formatter != ""){
				$chartJS .= "\n    " . $this->chart->renderTo . "ChartOptions.plotOptions.pie.dataLabels.formatter = function() { " . $this->plotOptions->pie->dataLabels->formatter . " };"; //example
			}
		}

		//rendered
		$chartJS .= "\n    var " . $this->chart->renderTo . " = new Highcharts.Chart(\n";
		$chartJS .= "       " . $this->chart->renderTo . "ChartOptions\n";
		$chartJS .= "    );\n";
		$chartJS .= "\n  });\n";
		return trim($chartJS);
	}

	/** returns HighRoller Object in a filtered array. No null or empty arrays are shown
	* @return array - filtered object to array
	*/
	private function toArray($obj) {
		$arrObj = is_object($obj) ? get_object_vars($obj) : $obj;

		foreach ($arrObj as $key => $val) {
			$val = (is_array($val) || is_object($val)) ? $this->toArray($val) : $val;
			// tests if val empty, and then do some exceptions
			if(!empty($val) || ($val === false) || ($val === 0))
				$arr[$key] = $val;
		}

		return (isset($arr)) ? $arr : NULL;
	}

	/** returns valid Highcharts javascript object containing your HighRoller options, for manipulation between the markup script tags on your page`
	* @return string - highcharts options object!
	*/
	function getChartOptionsObject(){
		$array = $this->toArray($this);
		return trim(json_encode($array));
	}

	/** returns new Highcharts.Chart() using your $varname
	* @param $varname - name of your javascript object holding getChartOptionsObject()
	* @return string - a new Highcharts.Chart() object with the highroller chart options object
	*/
	function renderChartOptionsObject($varname){
		return "new Highcharts.Chart(". $varname . ")";
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 8:56 PM
 * Desc: HighRoller xAxis Labels
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerAxisLabel {

	public $style;

	function __construct(){
		$this->style = new HighRollerStyle();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:10 PM
 * Desc: HighRoller xAxis Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerAxisTitle {

	public $style;

	function __construct(){
		$this->style = new HighRollerStyle();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/23/11
 * Time: 5:32 PM
 * Desc: HighRoller Background Color Options
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerBackgroundColors {

	public $linearGradient;
	public $stops;

	function __construct(){
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:04 PM
 * Desc: HighRoller Chart Class
 *
 *  Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerChart {

	public $renderTo;
	// public $animation;

	function __construct(){
		$this->renderTo = null;
		// $this->animation = new HighRollerChartAnimation();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:06 PM
 * Desc: HighRoller Chart Animation Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerChartAnimation {

	function __construct(){
	}
}
?>
<?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:10 PM
 * Desc: HighRoller Credits Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerCredits {

	function __construct(){
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/23/11
 * Time: 10:03 PM
 * Desc: HighRoller Data Labels
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerDataLabels {

	public $formatter = "";

	function __construct(){
		$this->style = new HighRollerStyle();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 10/9/11
 * Time: 11:27 PM
 * Desc: HighRoller Date Time Label Formats
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerDateTimeLabelFormats {

	function __construct(){
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 12:44 PM
 * Desc: HighRoller Engine Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerEngine {

	public $type;

	function __construct(){
		$this->type = "jquery";
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 11:48 PM
 * Desc: HighRoller Formatter
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerFormatter {

	public $formatter = null;

	function __construct(){
		// $this->formatter = "";
	}
}
?>
<?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:10 PM
 * Desc: HighRoller Legend Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerLegend {

	public $style;
	public $backgroundColor;
	public $enabled = true;

	function __construct(){
		$this->style = new HighRollerStyle();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:03 PM
 * Desc: HighRoller Options Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerOptions {

	public $global;

	function __construct(){
		$this->global = new HighRollerOptionsGlobal();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 12:48 PM
 * Desc: HighRoller Options Global Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerOptionsGlobal {

	public $useUTC;

	function __construct(){
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/23/11
 * Time: 12:38 PM
 * Desc: HighRoller Plot Options
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerPlotOptions {

	public $series;
	public $area = NULL;
	public $bar = NULL;
	public $column = NULL;
	public $line = NULL;
	public $pie = NULL;
	public $scatter = NULL;
	public $spline = NULL;

	function __construct($chartType){
		$this->series = new HighRollerPlotOptionsSeriesOptions();
		switch ($chartType) {
			case 'area': 	$this->area = new HighRollerPlotOptionsByChartType(); break;
			case 'bar':  	$this->bar = new HighRollerPlotOptionsByChartType(); break;
			case 'column':	$this->column = new HighRollerPlotOptionsByChartType(); break;
			case 'line':  	$this->line = new HighRollerPlotOptionsByChartType(); break;
			case 'pie':  	$this->pie = new HighRollerPlotOptionsByChartType(); break;
			case 'scatter': $this->scatter = new HighRollerPlotOptionsByChartType(); break;
			case 'spline':  $this->spline = new HighRollerPlotOptionsByChartType(); break;
		}
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/24/11
 * Time: 3:28 PM
 * Desc: HighRoller Plot Options By Chart Type
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerPlotOptionsByChartType {

	public $dataLabels;
	public $formatter = "";
	public $pointPadding;

	function __construct(){
		$this->dataLabels = new HighRollerDataLabels();
	}
}
?><?php
/**
 * Author: jfloff
 * Date: 12/24/12
 * Time: 15:30 PM
 * Desc: HighRoller Select Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerSelect {

	public $enabled;
	public $fillColor;
	public $lineColor;
	public $lineWidth;
	public $radius;

	function __construct(){
	}
}
?><?php
/**
 * Author: jfloff
 * Date: 12/24/12
 * Time: 15:30 PM
 * Desc: HighRoller Hover Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerHover {

	public $enabled;
	public $fillColor;
	public $lineColor;
	public $lineWidth;
	public $radius;

	function __construct(){
	}
}
?><?php
/**
 * Author: jfloff
 * Date: 12/24/12
 * Time: 15:30 PM
 * Desc: HighRoller States Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerStates {

	public $hover;
	public $select;

	function __construct(){
		$this->hover = new HighRollerHover();
		$this->select = new HighRollerSelect();
	}
}
?><?php
/**
 * Author: jfloff
 * Date: 12/24/12
 * Time: 15:30 PM
 * Desc: HighRoller Marker Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerMarker {

	public $enabled;
	public $fillColor;
	public $lineColor;
	public $lineWidth;
	public $radius;
	public $states;
	public $symbol;

	function __construct(){
		$this->states = new HighRollerStates();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:11 PM
 * Desc: HighRoller Series Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerSeries {

	public $name;
	public $data = array();
	public $marker;

	function __construct(){
		$this->marker = new HighRollerMarker();
	}

	/** add data to your series data
	* @param $chartdata - array or HighRollerSeriesData
	* @return void
	*/
	public function addData($chartdata){
		$this->data[] = $chartdata;
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:11 PM
 * Desc: HighRoller Series Data Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerSeriesData {

	public $name;
	public $data = array();

	/** add data to your series data
	* @param $chartdata - array or HighRollerSeriesData
	* @return void
	*/
	public function addData($chartdata){
		$this->data[] = $chartdata;
	}
}

?><?php
/**
 * Author: jmac
 * Date: 9/23/11
 * Time: 12:40 PM
 * Desc: HighRoller Series Options
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerPlotOptionsSeriesOptions {

	public $dataLabels;
	public $stacking = null;

	function __construct(){
		$this->dataLabels = new HighRollerDataLabels();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/24/11
 * Time: 1:28 AM
 * Desc: HighRoller Style
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerStyle {

	function __construct(){
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:07 PM
 * Desc: HighRoller Title Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerTitle {

	public $text;
	// public $style;

	function __construct(){
		// $this->style = new HighRollerStyle();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 11:46 PM
 * Desc: HighRoller Tool Tip
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerToolTip {

	public $backgroundColor = '#FFFFFF';
	public $formatter = "";

	function __construct(){
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 11:13 PM
 * Desc: HighRoller Plot Lines
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerPlotLines {

	public $color = '#aa4643';
	public $width = 3;
	public $value;

	function __construct(){
	}
}
?><?php
/**
 * Author: jfloff
 * Date: 12/16/12
 * Time: 13:00 PM
 * Desc: HighRoller Abstract Axis Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

abstract class HighRollerAxis {

	public $labels;
	public $title;
	public $categories = array();
	public $type;
	public $tickInterval;
	public $plotLines = array();	// @TODO instantiating a new plotLines object isn't working, setting as an array

	function __construct(){
		$this->title = new HighRollerAxisTitle();
		$this->dateTimeLabelFormats = new HighRollerDateTimeLabelFormats();
	}

	public function addPlotLines(HighRollerPlotLines $plotLines) {
		$this->plotLines[] = $plotLines;
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 1:10 PM
 * Desc: HighRoller xAxis Class
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerXAxis extends HighRollerAxis {

	function __construct(){
		parent::__construct();
		$this->labels = new HighRollerXAxisLabels();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 8:56 PM
 * Desc: HighRoller xAxis Labels
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerXAxisLabels {

	public $style;

	function __construct(){
		$this->style = new HighRollerStyle();
	}
}
?><?php
/**
 * Author: jmac
 * Date: 9/21/11
 * Time: 8:44 PM
 * Desc: HighRoller yAxis
 *
 * Licensed to Gravity.com under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.  Gravity.com licenses this file to you use
 * under the Apache License, Version 2.0 (the License); you may not this
 * file except in compliance with the License.  You may obtain a copy of the
 * License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

class HighRollerYAxis extends HighRollerAxis {

	function __construct(){
		parent::__construct();
	}
}
?>
