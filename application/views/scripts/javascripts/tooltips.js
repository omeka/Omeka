// Modified from https://zoebijl.github.io/apg-tooltip/

class Tooltip {
  constructor(element) {
    this.container = element
    this.trigger = element.querySelector('[data-tooltip-trigger]')
    this.tooltip = element.querySelector('[role=tooltip]')
    this.tooltipPosition = this.getTooltipPosition()
    this.globalEscapeBound = this.globalEscape.bind(this)
    this.globalPointerDownBound = this.globalPointerDown.bind(this)
    this.initialiseClassList()
    this.bindEvents()
  }

  // Basic actions
  openTooltip() {
    this.showTooltip()
    this.checkBoundingBox()
    this.attachGlobalListener()
  }

  closeTooltip() {
    this.hideTooltip()
    this.resetBoundingBox()
    this.removeGlobalListener()
  }

  // Binding event listteners
  bindEvents() {
    // Events that trigger openTooltip()
    // Open on mouse hover
    this.container.addEventListener('mouseenter', this.openTooltip.bind(this))
    // Open when a touch is detected
    this.container.addEventListener('touchstart', this.openTooltip.bind(this))
    // Open when the trigger gets focus
    this.trigger.addEventListener('focus', this.openTooltip.bind(this))

    // Events that trigger closeTooltip()
    // Close when the mouse cursor leaves the trigger or tooltip area
    this.container.addEventListener('mouseleave', this.closeTooltip.bind(this))
    // Close when the trigger loses focus
    this.trigger.addEventListener('blur', this.closeTooltip.bind(this))
  }

  attachGlobalListener() {
    document.addEventListener('keydown', this.globalEscapeBound)
    document.addEventListener('pointerdown', this.globalPointerDownBound)
  }

  removeGlobalListener() {
    document.removeEventListener('keydown', this.globalEscapeBound)
    document.removeEventListener('pointerdown', this.globalPointerDownBound)
  }

  globalEscape(event) {
    if (event.key === 'Escape' || event.key === 'Esc') {
      this.closeTooltip()
    }
  }

  // Close the tooltip if the target is anything other than the components within the tooltip widget
  globalPointerDown(event) {
    switch (event.target) {
      case this.container:
      case this.trigger:
      case this.tooltip:
        event.preventDefault()
        break
      default:
        this.closeTooltip()
        this.trigger.blur()
    }
  }

  // Show or hide the tooltip
  showTooltip() {
    this.container.classList.add('tooltip-visible')
    this.tooltip.classList.remove('hidden')
  }

  hideTooltip() {
    this.container.classList.remove('tooltip-visible')
    this.tooltip.classList.add('hidden')
  }

  // Get the desired default position for the tooltip (defaults to 'bottom')
  getTooltipPosition() {
    let attribute = this.container.getAttribute('data-tooltip-position')
    let setting = 'bottom'

    if (attribute === 'top') {
      setting = attribute
    }

    return setting;
  }

  // Set the default classes for tooltips based on this.getTooltipPosition()
  initialiseClassList() {
    switch (this.tooltipPosition) {
      case 'top':
        this.container.classList.add('top')
        break

      default:
        this.container.classList.remove('top')
        break
    }
  }

  // Calculate if the tooltip is within the viewport
  checkBoundingBox() {
    let bounds = this.tooltip.getBoundingClientRect()

    this.checkHorizontalBounding(bounds)
    this.checkVerticalBounding(bounds)
  }

  checkHorizontalBounding(bounds) {
    let windowWidth = window.innerWidth

    // If the tooltip overlaps on both sides, throw an error
    if (bounds.right > windowWidth && bounds.left < 0) {
      throw new Error('Tooltip width too wide for the window')
    }

    // Check if the right side of the tooltip is beyond the right side of the viewport
    if (bounds.right > windowWidth) {
      this.moveTooltipLeft(bounds, windowWidth)
    }

    // Check if the left side of the tooltip is beyond the left side of the viewport
    if (bounds.left < 0 ) {
      this.moveTooltipRight(bounds)
    }
  }

  checkVerticalBounding(bounds) {
    let windowHeight = window.innerHeight

    // If the tooltip overlaps on both sides, throw an error
    if (bounds.bottom > windowHeight && bounds.top < 0) {
      throw new Error('Tooltip height too high for the window')
    }

    // Check if the bottom of the tooltip is below the bottom of the viewport
    if (bounds.bottom > windowHeight) {
      this.moveTooltipUp()
    }

    // Check if the top of the tooltip is above the top of the viewport
    if (bounds.top < 20) {
      this.moveTooltipDown()
    }
  }

  // Move the tooltip so it fits within the viewport
  moveTooltipUp() {
    this.container.classList.add('top')
  }

  moveTooltipRight(bounds) {
    let numToMove = Math.floor(bounds.width / 2)
    this.tooltip.style.left = `${numToMove}px`
  }

  moveTooltipDown() {
    this.container.classList.remove('top')
  }

  moveTooltipLeft(bounds, windowWidth) {
    let translateAmount = (windowWidth - Math.round(bounds.right) - (Math.round(bounds.width) / 1.6))
    this.tooltip.style.transform = `translateX(${translateAmount}px)`
  }

  // Reset the changes made by the bounding box functions
  resetBoundingBox() {
    let tooltip = this.tooltip;
    if (tooltip.style.left || tooltip.style.transform) {
      tooltip.style.left = null
      tooltip.style.transform = null
    }

    this.initialiseClassList()
  }
}

Array.from(document.querySelectorAll('.tooltip-container')).forEach(element => new Tooltip(element))
