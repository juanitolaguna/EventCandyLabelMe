import EclmPlugin from "./eclm-plugin/eclm-plugin.plugin";

const PluginManager = window.PluginManager;
PluginManager.register('EclmPlugin', EclmPlugin, '[eclm-plugin]');

console.log('test');
