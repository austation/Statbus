
const getOrCreateLegendList = (chart, id) => {
    const legendContainer = document.getElementById(id);
    let listContainer = legendContainer.querySelector('ul');
  
    if (!listContainer) {
      listContainer = document.createElement('ul');
      listContainer.style.display = 'flex';
      listContainer.style.flexDirection = 'row';
      listContainer.style.margin = 0;
      listContainer.style.padding = 0;
  
      legendContainer.appendChild(listContainer);
    }
  
    return listContainer;
  };
  
  const htmlLegendPlugin = {
    id: 'htmlLegend',
    afterUpdate(chart, args, options) {
      const ul = getOrCreateLegendList(chart, options.containerID);
      ul.setAttribute('class','list-group mt-3')
      ul.setAttribute('style','')
      // Remove old legend items
      while (ul.firstChild) {
        ul.firstChild.remove();
      }
  
      // Reuse the built-in legendItems generator
      console.log(chart.options)
      const items = chart.options.plugins.legend.labels.generateLabels(chart);

      items.forEach(item => {
        const li = document.createElement('li');
          li.setAttribute('class','list-group-item')
  
        li.onclick = () => {
          const {type} = chart.config;
          if (type === 'pie' || type === 'doughnut') {
            chart.toggleDataVisibility(item.index);
          } else {
            chart.setDatasetVisibility(item.datasetIndex, !chart.isDatasetVisible(item.datasetIndex));
          }
          chart.update();
        };
  
        // Color box
        const boxSpan = document.createElement('span');
        boxSpan.setAttribute('class','badge')
        boxSpan.innerText = `${item.text}`
        boxSpan.style.background = item.fillStyle;
        boxSpan.style.textDecoration = item.hidden ? 'line-through' : '';
  
        li.appendChild(boxSpan);
        ul.appendChild(li);
      });
    }
  };
  function formatData(response, label) {
    let newFormat = {
      datasets: [
        {
          label: label,
          data: [],
          backgroundColor: [],
        },
      ],
      labels: [],
    };
    response.forEach((item) => {
      newFormat.datasets[0].data.push(item.minutes);
      newFormat.labels.push(item.job);
      newFormat.datasets[0].backgroundColor.push(item.background);
    });
    return newFormat;
  }
  Chart.defaults.maintainAspectRatio = false
  const ctx = document.getElementById("playtime");
  var options = {
    type: "bar",
    data: formatData({{playtime|json_encode()|raw}}, "Minutes"),
    options: {
      scales: {
        x: {
          type: 'logarithmic',
        }
      },
      indexAxis: 'y',
      parsing: {
        xAxisKey: "job",
        yAxisKey: "minutes",
        key: "job",
      },
      plugins: {
          legend: {
              display: false
          },
      },
    },
    // plugins: [htmlLegendPlugin],
  };
  const chart = new Chart(ctx, options)
  const height = chart.getSortedVisibleDatasetMetas()[0].data.length * 30 + 100
  const width = chart.canvas.parentNode.offsetWidth
  chart.canvas.parentNode.style.height = `${height}px`;
  chart.canvas.parentNode.style.width = `${chart.canvas.parentNode.style.width}px`;
  chart.render()
  