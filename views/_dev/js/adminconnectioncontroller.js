/* global $, prestashop */

import Grid from './components/grid/grid';
import FiltersResetExtension from "./components/grid/extension/filters-reset-extension";
import SortingExtension from "./components/grid/extension/sorting-extension";
import ReloadListExtension from "./components/grid/extension/reload-list-extension";
import ColumnTogglingExtension from "./components/grid/extension/column-toggling-extension";

const $ = window.$;

$(document).ready(() => {
  const theGrid = new Grid('adminconnectiongrid');
  theGrid.addExtension(new FiltersResetExtension());
  theGrid.addExtension(new SortingExtension());
  theGrid.addExtension(new ColumnTogglingExtension());
});
