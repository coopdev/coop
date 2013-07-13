function jqPlotDefaultOptions() {
   options = 
   {
      title: 'a',
      seriesDefaults:{
          renderer:$.jqplot.BarRenderer,
          rendererOptions: {fillToZero: true},
          pointLabels: { show: true, location: 'n', edgeTolerance: -25 }
      },
      series:[
          //{label:'Number of Students'},
      ],
      axes: {
          // Use a category axis on the x axis and use our custom ticks.
          xaxis: {
             renderer: $.jqplot.CategoryAxisRenderer
          },
          yaxis: {
             min: 0 
          }
      }
   }
   return options
}