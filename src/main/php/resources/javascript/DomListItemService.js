/*
<template id = "tplListItemService">
	<li>
		<div class = "listItemService">
			<div class = "metricIndicatorContainer">
				<span class = "metricIndicator "></span>

				<img src = "" hidden />
			</div>
			<div class = "metricDetail">
				<div class = "metricText">	
					<a href = "viewService.php?identifier="><span class = "serviceTitle">???</span></a>
				</div>
				<span class = "node">
					<a href = "viewNode.php?identifier=?">???</a>
				</span>
			</div>
		</div>
	</li>
</template>
*/

export default class DomListItemService  {
  constructor(container, classRequirement = false, serviceTitle = false) {
    this.domMetricIndicator = document.createElement('span')
    this.domMetricIndicator.classList.add('metricIndicator')
    container.appendChild(this.domMetricIndicator)

    if (classRequirement) {
      this.domClassRequirement = document.createElement('span')
      this.domClassRequirement.classList.add('classRequirement')
      container.appendChild(this.domClassRequirement)
    }

    this.domNode = document.createElement('span')
    this.domNode.classList.add('node')
    container.appendChild(this.domNode)

    this.domMetricText = document.createElement('span')
    this.domMetricText.classList.add('metricText')
    container.appendChild(this.domMetricText)
  }

  setKarma(karma, req) {
    if (karma === null) {
      karma = 'bad';
    }

    this.domMetricIndicator.classList.add(karma.toLowerCase())
    this.domMetricIndicator.innerText = '?'
  }

  setIcon(icon) {
    if (icon === undefined) {
//      this.querySelector('img').src = 'resources/images/serviceIcons/' + icon
    }
  }

  setDescription(desc) {
//    this.querySelector('span.description').innerText = desc
  }

  setLastChangedRelative(lastChanged) {
    this.domMetricIndicator.innerText = lastChanged
  }

  setTitleId(title, id) {
    this.domMetricText.innerText = title
    this.domMetricText.setAttribute('href', 'viewService.php?identifier=' + id)
  }

  setNode(node) {
    let link = this.domNode

    if (node == null) {
      link.innerText = 'no node'
      link.setAttribute('href', '#');
      link.classList.add('bad')
    } else {
      link.innerText = node
      link.setAttribute('href', 'viewNode.php?identifier=' + node)
    }
  }

  setRequirement(requirement) {  
    this.domClassRequirement.innerText = requirement['owningClassTitle'] + '/' + requirement['requirementTitle'];

    let txt = this.domMetricText

    if (requirement['serviceIdentifier'] == null) {
      txt.innerText = 'not covered'
      txt.classList.add('bad')
      txt.setAttribute('href', 'addInstanceCoverage.php?requirementId=' + requirement['requirementId'] + '&instanceId=' + requirement['instanceId'])
    } else {
      txt.innerText = requirement['serviceIdentifier']
      txt.classList.add(requirement['karma'])
    }
  }
}
