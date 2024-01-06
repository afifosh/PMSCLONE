import 'formiojs';

const PanelComponent = Formio.Components.components.panel;
export default class Page extends PanelComponent {
  static schema(...extend) {
    return PanelComponent.schema({
      label: 'Page',
      type: 'panel',
      key: 'page',
      title: 'Page',
      theme: 'default',
      breadcrumb: 'default',
      components: [],
      clearOnHide: false,
      input: false,
      tableView: false,
      persistent: false
    }, ...extend);
  }

  static get builderInfo() {
    return {
      title: 'Page',
      icon: 'list-alt',
      group: 'layout',
      documentation: '/userguide/form-building/layout-components#panel',
      weight: 30,
      schema: Page.schema()
    };
  }

  get defaultSchema() {
    return Page.schema();
  }

  get templateName() {
    return 'panel';
  }

  static savedValueTypes() {
    return [];
  }

  constructor(...args) {
    super(...args);
    this.noField = true;
    this.on('componentError', () => {
      //change collapsed value only when the panel is collapsed to avoid additional redrawing that prevents validation messages
      if (hasInvalidComponent(this) && this.collapsed) {
        this.collapsed = false;
      }
    });
  }

  getComponent(path, fn, originalPath) {
    if (this.root?.parent instanceof FormComponent) {
      path = path.replace(this._parentPath, '');
    }
    return super.getComponent(path, fn, originalPath);
  }
}

