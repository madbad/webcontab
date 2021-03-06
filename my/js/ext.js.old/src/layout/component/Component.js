/*

This file is part of Ext JS 4

Copyright (c) 2011 Sencha Inc

Contact:  http://www.sencha.com/contact

GNU General Public License Usage
This file may be used under the terms of the GNU General Public License version 3.0 as published by the Free Software Foundation and appearing in the file LICENSE included in the packaging of this file.  Please review the following information to ensure the GNU General Public License version 3.0 requirements will be met: http://www.gnu.org/copyleft/gpl.html.

If you are unsure which license is appropriate for your use, please contact the sales department at http://www.sencha.com/contact.

*/
/**
 * @class Ext.layout.component.Component
 * @extends Ext.layout.Layout
 *
 * This class is intended to be extended or created via the {@link Ext.Component#componentLayout layout}
 * configuration property.  See {@link Ext.Component#componentLayout} for additional details.
 *
 * @private
 */
Ext.define('Ext.layout.component.Component', {

    /* Begin Definitions */

    extend: 'Ext.layout.Layout',

    /* End Definitions */

    type: 'component',

    monitorChildren: true,

    initLayout : function() {
        var me = this,
            owner = me.owner,
            ownerEl = owner.el;

        if (!me.initialized) {
            if (owner.frameSize) {
                me.frameSize = owner.frameSize;
            }
            else {
                owner.frameSize = me.frameSize = {
                    top: 0,
                    left: 0,
                    bottom: 0,
                    right: 0
                };
            }
        }
        me.callParent(arguments);
    },

    beforeLayout : function(width, height, isSetSize, callingContainer) {
        this.callParent(arguments);

        var me = this,
            owner = me.owner,
            ownerCt = owner.ownerCt,
            layout = owner.layout,
            isVisible = owner.isVisible(true),
            ownerElChild = owner.el.child,
            layoutCollection;

        // Cache the size we began with so we can see if there has been any effect.
        me.previousComponentSize = me.lastComponentSize;

        // Do not allow autoing of any dimensions which are fixed
        if (!isSetSize
            && ((!Ext.isNumber(width) && owner.isFixedWidth()) ||
                (!Ext.isNumber(height) && owner.isFixedHeight()))
            // unless we are being told to do so by the ownerCt's layout
            && callingContainer && callingContainer !== ownerCt) {
            
            me.doContainerLayout();
            return false;
        }

        // If an ownerCt is hidden, add my reference onto the layoutOnShow stack.  Set the needsLayout flag.
        // If the owner itself is a directly hidden floater, set the needsLayout object on that for when it is shown.
        if (!isVisible && (owner.hiddenAncestor || owner.floating)) {
            if (owner.hiddenAncestor) {
                layoutCollection = owner.hiddenAncestor.layoutOnShow;
                layoutCollection.remove(owner);
                layoutCollection.add(owner);
            }
            owner.needsLayout = {
                width: width,
                height: height,
                isSetSize: false
            };
        }

        if (isVisible && this.needsLayout(width, height)) {
            return owner.beforeComponentLayout(width, height, isSetSize, callingContainer);
        }
        else {
            return false;
        }
    },

    /**
    * Check if the new size is different from the current size and only
    * trigger a layout if it is necessary.
    * @param {Number} width The new width to set.
    * @param {Number} height The new height to set.
    */
    needsLayout : function(width, height) {
        var me = this,
            widthBeingChanged,
            heightBeingChanged;
            me.lastComponentSize = me.lastComponentSize || {
                width: -Infinity,
                height: -Infinity
            };

        // If autoWidthing, or an explicitly different width is passed, then the width is being changed.
        widthBeingChanged  = !Ext.isDefined(width)  || me.lastComponentSize.width  !== width;

        // If autoHeighting, or an explicitly different height is passed, then the height is being changed.
        heightBeingChanged = !Ext.isDefined(height) || me.lastComponentSize.height !== height;


        // isSizing flag added to prevent redundant layouts when going up the layout chain
        return !me.isSizing && (me.childrenChanged || widthBeingChanged || heightBeingChanged);
    },

    /**
    * Set the size of any element supporting undefined, null, and values.
    * @param {Number} width The new width to set.
    * @param {Number} height The new height to set.
    */
    setElementSize: function(el, width, height) {
        if (width !== undefined && height !== undefined) {
            el.setSize(width, height);
        }
        else if (height !== undefined) {
            el.setHeight(height);
        }
        else if (width !== undefined) {
            el.setWidth(width);
        }
    },

    /**
     * Returns the owner component's resize element.
     * @return {Ext.Element}
     */
     getTarget : function() {
         return this.owner.el;
     },

    /**
     * <p>Returns the element into which rendering must take place. Defaults to the owner Component's encapsulating element.</p>
     * May be overridden in Component layout managers which implement an inner element.
     * @return {Ext.Element}
     */
    getRenderTarget : function() {
        return this.owner.el;
    },

    /**
    * Set the size of the target element.
    * @param {Number} width The new width to set.
    * @param {Number} height The new height to set.
    */
    setTargetSize : function(width, height) {
        var me = this;
        me.setElementSize(me.owner.el, width, height);

        if (me.owner.frameBody) {
            var targetInfo = me.getTargetInfo(),
                padding = targetInfo.padding,
                border = targetInfo.border,
                frameSize = me.frameSize;

            me.setElementSize(me.owner.frameBody,
                Ext.isNumber(width) ? (width - frameSize.left - frameSize.right - padding.left - padding.right - border.left - border.right) : width,
                Ext.isNumber(height) ? (height - frameSize.top - frameSize.bottom - padding.top - padding.bottom - border.top - border.bottom) : height
            );
        }

        me.autoSized = {
            width: !Ext.isNumber(width),
            height: !Ext.isNumber(height)
        };

        me.lastComponentSize = {
            width: width,
            height: height
        };
    },

    getTargetInfo : function() {
        if (!this.targetInfo) {
            var target = this.getTarget(),
                body = this.owner.getTargetEl();

            this.targetInfo = {
                padding: {
                    top: target.getPadding('t'),
                    right: target.getPadding('r'),
                    bottom: target.getPadding('b'),
                    left: target.getPadding('l')
                },
                border: {
                    top: target.getBorderWidth('t'),
                    right: target.getBorderWidth('r'),
                    bottom: target.getBorderWidth('b'),
                    left: target.getBorderWidth('l')
                },
                bodyMargin: {
                    top: body.getMargin('t'),
                    right: body.getMargin('r'),
                    bottom: body.getMargin('b'),
                    left: body.getMargin('l')
                }
            };
        }
        return this.targetInfo;
    },

    // Start laying out UP the ownerCt's layout when flagged to do so.
    doOwnerCtLayouts: function() {
        var owner = this.owner,
            ownerCt = owner.ownerCt,
            ownerCtComponentLayout, ownerCtContainerLayout,
            curSize = this.lastComponentSize,
            prevSize = this.previousComponentSize,
            widthChange  = (prevSize && curSize && Ext.isNumber(curSize.width )) ? curSize.width  !== prevSize.width  : true,
            heightChange = (prevSize && curSize && Ext.isNumber(curSize.height)) ? curSize.height !== prevSize.height : true;

        // If size has not changed, do not inform upstream layouts
        if (!ownerCt || (!widthChange && !heightChange)) {
            return;
        }

        ownerCtComponentLayout = ownerCt.componentLayout;
        ownerCtContainerLayout = ownerCt.layout;

        if (!owner.floating && ownerCtComponentLayout && ownerCtComponentLayout.monitorChildren && !ownerCtComponentLayout.layoutBusy) {
            if (!ownerCt.suspendLayout && ownerCtContainerLayout && !ownerCtContainerLayout.layoutBusy) {

                // If the owning Container may be adjusted in any of the the dimension which have changed, perform its Component layout
                if (((widthChange && !ownerCt.isFixedWidth()) || (heightChange && !ownerCt.isFixedHeight()))) {
                    // Set the isSizing flag so that the upstream Container layout (called after a Component layout) can omit this component from sizing operations
                    this.isSizing = true;
                    ownerCt.doComponentLayout();
                    this.isSizing = false;
                }
                // Execute upstream Container layout
                else if (ownerCtContainerLayout.bindToOwnerCtContainer === true) {
                    ownerCtContainerLayout.layout();
                }
            }
        }
    },

    doContainerLayout: function() {
        var me = this,
            owner = me.owner,
            ownerCt = owner.ownerCt,
            layout = owner.layout,
            ownerCtComponentLayout;

        // Run the container layout if it exists (layout for child items)
        // **Unless automatic laying out is suspended, or the layout is currently running**
        if (!owner.suspendLayout && layout && layout.isLayout && !layout.layoutBusy && !layout.isAutoDock) {
            layout.layout();
        }

        // Tell the ownerCt that it's child has changed and can be re-layed by ignoring the lastComponentSize cache.
        if (ownerCt && ownerCt.componentLayout) {
            ownerCtComponentLayout = ownerCt.componentLayout;
            if (!owner.floating && ownerCtComponentLayout.monitorChildren && !ownerCtComponentLayout.layoutBusy) {
                ownerCtComponentLayout.childrenChanged = true;
            }
        }
    },

    afterLayout : function(width, height, isSetSize, layoutOwner) {
        this.doContainerLayout();
        this.owner.afterComponentLayout(width, height, isSetSize, layoutOwner);
    }
});

