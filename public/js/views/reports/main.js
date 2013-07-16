function jqPlotDefaultOptions() {
   options = 
   {
      //title: 'a',
      seriesDefaults:{
          renderer:$.jqplot.BarRenderer,
          pointLabels: { show: true, location: 'e', edgeTolerance: -25 },
          shadowAngle: 135,
          rendererOptions: {
             barDirection: 'horizontal'
          }
      },
      axes: {
          // Use a category axis on the x axis and use our custom ticks.
          //xaxis: {
          //   //min: 0 
          //},
          yaxis: {
             renderer: $.jqplot.CategoryAxisRenderer
          }
      }
   }
   return options
}