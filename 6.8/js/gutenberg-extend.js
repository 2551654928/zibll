(function ($) {
    $(document).ready(function () {
        console.log("子比主题：增强编辑器");
        var b = wp.blocks,
            c = wp.components,
            e = wp.element,
            ed = wp.blockEditor ? wp.blockEditor : wp.editor,
            rE = wp.richText.registerFormatType,
            _data = wp.data,
            dispatch = _data.dispatch,
            select = _data.select,
            _dispatch = dispatch('core/block-editor') ? dispatch('core/block-editor') : dispatch('core/editor'),
            _select = select('core/block-editor') ? select('core/block-editor') : select('core/editor'),
            getBlockOrder = _select.getBlockOrder,
            getBlock = _select.getBlock,
            insertBlock = _dispatch.insertBlock,
            removeBlock = _dispatch.removeBlock,
            updateBlockAttributes = _dispatch.updateBlockAttributes,

            el = e.createElement,
            rB = b.registerBlockType,

            createBlock = b.createBlock,
            InnerBlocks = ed.InnerBlocks,
            RichTextToolbarButton = ed.RichTextToolbarButton,
            Component = e.Component,
            getRectangleFromRange = wp.dom.getRectangleFromRange,
            Popover = c.Popover,
            Button = c.Button,
            RichText = ed.RichText,
            PlainText = ed.PlainText,
            MediaUploadCheck = ed.MediaUploadCheck,
            MediaUpload = ed.MediaUpload,
            Fragment = e.Fragment,
            InspectorControls = ed.InspectorControls,
            PanelBody = c.PanelBody,
            ClipboardButton = c.ClipboardButton,
            TextControl = c.TextControl,
            RadioControl = c.RadioControl,
            Toolbar = c.Toolbar,
            SelectControl = c.SelectControl,
            ToggleControl = c.ToggleControl,
            CheckboxControl = CheckboxControl,
            RangeControl = c.RangeControl,
            DropdownMenu = c.DropdownMenu,
            BlockControls = ed.BlockControls,
            AlignmentToolbar = ed.AlignmentToolbar,
            _lodash = lodash,
            times = _lodash.times,
            throttle = _lodash.throttle,
            debounce = _lodash.debounce;

        var icon = {};
        var icon_color = '#1fbc45';
        var xmlns = "http://www.w3.org/2000/svg";

        icon.zibll = el("svg", {
            width: "16px",
            height: "16px",
            viewBox: "0 0 80 90",
            className: "svg-icon",
            xmlns: xmlns,
            fillRule: "evenodd",
            clipRule: "evenodd",
            strokeLinejoin: "round",
            strokeMiterlimit: "1.414"
        }, el("g", {
            fillRule: "nonzero",
            transform: "scale(0.19785394)"
        }, el("path", {
            d: "m 408.68862,333.8044 0.009,0.59315 0.004,0.29795 c 0.18694,71.91774 -59.58485,130.93907 -134.53126,132.29044 -0.85543,0.0154 -1.71097,0.0231 -2.56654,0.0231 H 21.034693 C 11.007191,467.00905 2.5761396,460.42672 0.14656613,451.50411 L 338.63176,215.013 c 45.60925,25.97569 68.96153,65.57282 70.05686,118.7914 z M 231.25625,0 C 278.01331,0 321.188,23.909861 344.73248,62.682399 L 0,303.53751 V 20.793975 C 0,9.3097796 9.6674353,0 21.592821,0 Z",
            fill: icon_color
        })));

        icon.video = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M682.666667 405.333333h64v426.666667H256c0-234.666667 192-426.666667 426.666667-426.666667z",
            fill: icon_color
        }), el("path", {
            d: "M704 874.666667H128c-46.933333 0-85.333333-38.4-85.333333-85.333334V234.666667c0-46.933333 38.4-85.333333 85.333333-85.333334h576c46.933333 0 85.333333 38.4 85.333333 85.333334v554.666666c0 46.933333-38.4 85.333333-85.333333 85.333334zM128 213.333333c-12.8 0-21.333333 8.533333-21.333333 21.333334v554.666666c0 12.8 8.533333 21.333333 21.333333 21.333334h576c12.8 0 21.333333-8.533333 21.333333-21.333334V234.666667c0-12.8-8.533333-21.333333-21.333333-21.333334H128z",
            fill: icon_color
        }), el("path", {
            d: "M277.333333 490.666667c-59.733333 0-106.666667-46.933333-106.666666-106.666667s46.933333-106.666667 106.666666-106.666667 106.666667 46.933333 106.666667 106.666667-46.933333 106.666667-106.666667 106.666667z m0-149.333334c-23.466667 0-42.666667 19.2-42.666666 42.666667s19.2 42.666667 42.666666 42.666667 42.666667-19.2 42.666667-42.666667-19.2-42.666667-42.666667-42.666667zM938.666667 800c-8.533333 0-14.933333-2.133333-21.333334-6.4L725.333333 682.666667V341.333333l192-110.933333c10.666667-6.4 21.333333-6.4 32-4.266667 10.666667 2.133333 21.333333 8.533333 25.6 19.2 4.266667 6.4 6.4 14.933333 6.4 21.333334v490.666666c0 23.466667-19.2 42.666667-42.666666 42.666667z m-149.333334-153.6l128 74.666667V302.933333l-128 74.666667v268.8z",
            fill: icon_color
        }));

        icon.iframe = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M885.76 52.053333h-364.8c-47.786667 0-86.186667 38.826667-86.186667 86.186667v82.346667H139.093333c-48.213333 0-87.466667 39.253333-87.466666 87.04v576.426666c0 48.213333 39.253333 87.466667 87.466666 87.466667h576.426667c48.213333 0 87.04-39.253333 87.04-87.466667v-294.826666h82.773333c47.786667 0 86.186667-38.826667 86.186667-86.186667V138.666667c0.426667-47.786667-37.973333-86.613333-85.76-86.613334z m-151.04 832.426667c0 10.666667-8.533333 19.2-19.2 19.2H139.093333c-10.666667 0-19.2-8.533333-19.2-19.2V308.053333c0-10.666667 8.533333-19.2 19.2-19.2h295.253334v214.186667c0 12.373333 2.56 24.32 7.68 34.986667L255.146667 725.333333v-68.693333c0-18.773333-15.36-34.133333-34.133334-34.133333s-34.133333 15.36-34.133333 34.133333v151.04a33.109333 33.109333 0 0 0 9.386667 23.466667c0.426667 0.426667 0.426667 0.426667 0.426666 0.853333 0.426667 0.426667 0.853333 0.426667 1.28 0.853333 2.986667 2.56 5.973333 5.12 9.813334 6.4 4.266667 1.706667 8.533333 2.56 13.226666 2.56h151.04c18.773333 0 34.133333-15.36 34.133334-34.133333s-15.36-34.133333-34.133334-34.133333H303.36l189.013333-189.013334c8.96 2.986667 18.346667 5.12 28.586667 5.12h213.76v294.826667z m168.96-381.013333c0 9.813333-8.106667 17.92-17.92 17.92h-364.8c-9.813333 0-17.92-8.106667-17.92-17.92V138.666667c0-9.813333 8.106667-17.92 17.92-17.92h364.8c9.813333 0 17.92 8.106667 17.92 17.92v364.8z",
            fill: icon_color
        }))

        icon.feature = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M833.176 606.564c37.737-60.688 59.927-131.977 59.927-208.47 0-218.921-178.605-397.021-398.09-397.021-219.534 0-398.178 178.1-398.178 397.021 0 87.873 29.126 168.877 77.787 234.72L30.118 883.48l156.358 11.582L275.333 1024l136.88-237.615c26.767 5.693 54.414 8.782 82.806 8.782 38.717 0 76.062-5.787 111.49-16.139L747.663 1024l88.858-128.938 156.314-11.582-159.659-276.916zM362.37 772.156L271.496 929.8l-57.172-83.017-100.66-7.458 95.25-165.296c0.79 0.832 1.719 1.474 2.509 2.307 9.742 9.86 19.876 19.33 30.555 28.111 2.364 1.917 4.918 3.581 7.283 5.448 8.902 6.966 18.004 13.636 27.45 19.872 3.641 2.401 7.384 4.463 11.07 6.72 9.104 5.542 18.353 10.794 27.9 15.603 3.002 1.57 6.05 2.995 9.154 4.414a397.407 397.407 0 0 0 34.93 14.575c0.885 0.342 1.72 0.782 2.605 1.078z m43.495-39.2a355.38 355.38 0 0 1-51.657-18.007 8.63 8.63 0 0 0-0.79-0.346c-15.4-6.822-30.112-14.916-44.236-23.843-1.62-1.035-3.344-1.967-4.92-2.995-13.377-8.731-26.027-18.592-38.076-29.146a321.702 321.702 0 0 1-7.632-6.916c-11.853-10.988-23.17-22.664-33.55-35.424-48.562-59.66-77.789-135.564-77.789-218.185 0-191.2 156.018-346.781 347.799-346.781 191.737 0 347.704 155.58 347.704 346.781 0 73.108-22.88 140.862-61.749 196.844-8.705 12.509-18.347 24.336-28.633 35.57-2.263 2.503-4.426 5.006-6.74 7.408-11.461 11.826-23.669 22.916-36.655 33.023-1.379 1.078-2.807 2.06-4.185 3.139-28.487 21.441-60.174 38.76-94.022 50.682-1.72 0.587-3.394 1.273-5.115 1.865l-8.611 2.995a347.098 347.098 0 0 1-101.994 15.307c-30.847 0-60.662-4.42-89.149-11.972z m402.807 113.826l-57.17 83.017-96.925-168.19c13.58-5.938 26.711-12.76 39.457-20.167 1.625-0.934 3.198-1.917 4.773-2.895 10.773-6.43 21.154-13.346 31.193-20.703a354.339 354.339 0 0 0 7.971-5.94c8.657-6.67 16.925-13.686 24.992-21.05 3.15-2.843 6.298-5.542 9.345-8.435 7.53-7.313 14.616-15.011 21.507-22.86 2.36-2.654 5.064-5.006 7.327-7.754L909.29 839.324l-100.617 7.458z",
            fill: icon_color
        }), el("path", {
            d: "M654.672 637.872l-30.548-177.414 129.3-125.559-178.65-25.904-79.71-161.369-79.95 161.369-178.693 25.904 129.343 125.56-30.503 177.361 159.753-83.753 159.658 83.805z m-309.915-267.94l103.814-15.016 46.398-93.76 46.347 93.76 103.765 15.017L570 442.889l17.76 103.033-92.74-48.671-92.845 48.721 17.713-103.083-75.13-72.956z",
            fill: icon_color
        }));

        icon.biaoti = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M640 76.8c-25.6 0-51.2 25.6-51.2 51.2v332.8H179.2V128c0-25.6-19.2-51.2-51.2-51.2s-51.2 25.6-51.2 51.2v704c0 25.6 19.2 51.2 51.2 51.2s51.2-19.2 51.2-51.2V563.2h416V832c0 25.6 19.2 51.2 51.2 51.2s51.2-19.2 51.2-51.2V128c-6.4-25.6-32-51.2-57.6-51.2zM915.2 358.4c-19.2-6.4-38.4 0-51.2 12.8l-102.4 108.8c-19.2 19.2-19.2 51.2 0 70.4 19.2 19.2 51.2 19.2 70.4 0l19.2-19.2v326.4c0 25.6 19.2 51.2 51.2 51.2s51.2-19.2 51.2-51.2v-448c-6.4-25.6-19.2-38.4-38.4-51.2z",
            fill: icon_color
        }));

        icon.postsbox = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M544.256 887.97866668c-9.728 0-19.456-2.56-28.16-8.192-16.896-10.24-27.136-28.16-26.624-48.128V269.99466668c0-24.576 13.824-47.616 35.84-58.368 117.76-60.416 260.608-90.112 423.936-87.04 35.84 1.024 65.024 30.208 65.024 66.048V731.30666668c0 33.792-25.088 61.44-58.88 65.024l-20.992 2.56c-141.312 15.36-263.168 29.184-364.032 82.944-8.192 4.096-17.408 6.144-26.112 6.144zM928.768 175.78666668c-146.432 0-274.432 27.648-380.416 81.92-4.608 2.56-7.68 7.168-7.68 12.8v562.688c0 1.536 0.512 2.56 2.048 3.584s2.56 0.512 3.072 0h0.512c109.568-58.368 236.032-72.192 382.976-88.576l20.992-2.56c7.68-0.512 13.312-6.656 13.312-13.824V190.63466668c0-8.192-6.656-14.848-14.848-14.848h-19.968z",
            fill: icon_color
        }), el("path", {
            d: "M487.424 889.51466668c-8.704 0-17.408-2.048-25.6-6.656C358.4 827.05066668 232.448 813.73866668 87.04 797.86666668l-13.312-1.536c-33.28-3.072-58.368-30.72-58.368-64.512V191.14666668c0-35.328 29.184-65.024 65.024-66.048 163.84-2.56 306.688 26.624 424.448 87.552 21.504 10.752 35.328 33.792 35.328 57.856V836.26666668c0 18.944-9.728 36.352-26.112 46.08-8.192 4.608-17.408 7.168-26.624 7.168z m-386.56-713.216H81.408c-8.192 0-14.848 6.656-14.848 14.848v540.672c0 7.168 5.632 13.312 12.8 13.824l13.312 1.536c151.04 15.872 281.088 30.208 393.728 90.624h0.512c0.512 0.512 1.024 0.512 1.536 0s1.024-1.024 1.024-1.536V270.50666668c0-5.12-3.072-10.24-7.168-12.288-106.496-54.784-234.496-81.92-381.44-81.92z",
            fill: icon_color
        }), el("path", {
            d: "M386.56 416.93866668c-3.072 0-6.656-0.512-9.728-2.048-67.584-27.136-136.704-40.96-204.288-40.96-14.336 0-25.6-11.264-25.6-25.6s11.264-25.6 25.6-25.6c74.24 0 149.504 14.848 223.744 45.056 13.312 5.12 19.456 19.968 14.336 33.28-4.096 9.728-13.824 15.872-24.064 15.872z m0 171.52c-3.072 0-6.656-0.512-9.728-2.048-67.584-27.136-136.704-40.96-204.288-40.96-14.336 0-25.6-11.264-25.6-25.6s11.264-25.6 25.6-25.6c74.24 0 149.504 14.848 223.744 45.056 13.312 5.12 19.456 19.968 14.336 33.28-4.096 9.728-13.824 15.872-24.064 15.872z m256.512-171.52c-10.24 0-19.968-6.144-23.552-15.872-5.12-13.312 1.024-28.16 14.336-33.28 73.728-29.696 148.992-45.056 223.744-45.056 14.336 0 25.6 11.264 25.6 25.6s-11.264 25.6-25.6 25.6c-68.096 0-136.704 13.824-204.288 40.96-3.584 1.536-7.168 2.048-10.24 2.048z m0 171.52c-10.24 0-19.968-6.144-23.552-15.872-5.12-13.312 1.024-28.16 14.336-33.28 73.728-29.696 148.992-45.056 223.744-45.056 14.336 0 25.6 11.264 25.6 25.6s-11.264 25.6-25.6 25.6c-68.096 0-136.704 13.824-204.288 40.96-3.584 1.536-7.168 2.048-10.24 2.048z",
            fill: icon_color
        }));

        icon.enlighter = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M512 0a512 512 0 1 1 0 1024A512 512 0 0 1 512 0z m0 73.142857a438.857143 438.857143 0 1 0 0 877.714286A438.857143 438.857143 0 0 0 512 73.142857z m50.834286 150.674286a36.571429 36.571429 0 0 1 29.622857 42.422857l-88.868572 504.246857a36.571429 36.571429 0 0 1-72.045714-12.726857l88.868572-504.246857a36.571429 36.571429 0 0 1 42.422857-29.622857zM341.430857 382.317714a36.571429 36.571429 0 0 1 0 51.785143l-77.531428 77.531429 77.531428 77.531428a36.571429 36.571429 0 0 1-51.712 51.785143L186.221714 537.453714a36.571429 36.571429 0 0 1 0-51.712l103.497143-103.497143a36.571429 36.571429 0 0 1 51.712 0z m388.827429-4.169143l5.12 4.169143 103.424 103.497143a36.571429 36.571429 0 0 1 4.242285 46.665143l-4.242285 5.046857-103.424 103.424a36.571429 36.571429 0 0 1-55.954286-46.665143l4.169143-5.12 77.604571-77.531428-77.531428-77.531429a36.571429 36.571429 0 0 1 46.592-55.954286z",
            fill: icon_color
        }));
        icon.buttons = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M769 468.7c5.5-22.1 11.1-49.7 11.1-77.4 0-44.2-11.1-88.4-27.6-127.1-16.6-38.7-38.7-71.9-71.9-105-27.6-27.6-60.8-49.7-99.5-66.3-38.7-16.6-82.9-27.7-127.1-27.7s-88.4 11.1-127.1 27.6-77.4 38.7-105 71.9c-27.6 27.6-55.3 66.3-71.9 105s-22.1 77.4-22.1 127.1c0 38.7 5.5 71.9 16.6 105 11.1 33.2 27.6 60.8 49.7 88.4s44.2 49.7 77.4 71.9c27.6 22.1 60.8 33.2 94 44.2V584.8c-38.7-16.6-66.3-44.2-88.4-77.4-22.1-33.2-33.2-71.9-33.2-110.6 0-27.6 5.5-55.3 16.6-82.9s27.6-49.7 44.2-66.3c16.6-22.1 38.7-33.2 66.3-44.2 27.6-11.1 55.3-16.6 82.9-16.6s55.3 5.5 82.9 16.6c22.1 11.1 44.2 22.1 60.8 44.2 16.6 16.6 33.2 38.7 44.2 66.3 11.1 27.6 16.6 55.3 16.6 82.9v5.5c0 0.1 110.5 66.4 110.5 66.4z",
            fill: icon_color
        }), el("path", {
            d: "M426.3 866.7c11.1-16.6 27.6-33.2 44.2-49.7 11.1-11.1 27.6-27.6 49.7-44.2 16.6-16.6 38.7-27.6 60.8-44.2L669.5 922c11.1 16.6 22.1 27.6 44.2 33.2 16.6 5.5 38.7 5.5 55.3-5.5 16.6-11.1 27.6-22.1 33.2-44.2 5.5-16.6 5.5-38.7-5.5-55.3L686.1 684.3c27.6-5.5 49.7-11.1 77.4-16.6 27.6-5.5 49.7-5.5 66.3-5.5h66.3L420.8 291.8l5.5 574.9z",
            fill: icon_color
        }));
        icon.tabs = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M420.608 489.984a15.104 15.104 0 0 1 4.864 11.776 14.848 14.848 0 0 1-4.864 11.776 17.664 17.664 0 0 1-12.288 4.352h-44.288V665.6a15.104 15.104 0 0 1-5.376 12.032 18.176 18.176 0 0 1-13.056 5.12 17.664 17.664 0 0 1-13.056-5.12 15.872 15.872 0 0 1-5.12-12.032v-147.712h-44.032a16.64 16.64 0 0 1-12.288-4.608 14.848 14.848 0 0 1-4.864-11.776 14.848 14.848 0 0 1 4.864-11.52 17.152 17.152 0 0 1 12.288-4.352H409.6a16.384 16.384 0 0 1 11.008 4.352zM557.824 534.016a15.872 15.872 0 0 1 4.864 12.288V665.6a15.616 15.616 0 0 1-4.864 12.032 15.872 15.872 0 0 1-12.032 5.12 16.64 16.64 0 0 1-12.032-4.864 17.664 17.664 0 0 1-4.864-12.032 61.952 61.952 0 0 1-44.032 19.712 64.512 64.512 0 0 1-35.328-9.984 69.376 69.376 0 0 1-25.6-27.904 92.16 92.16 0 0 1 0-80.384 67.072 67.072 0 0 1 25.6-27.904 61.696 61.696 0 0 1 34.304-9.984 66.816 66.816 0 0 1 45.312 17.408 16.64 16.64 0 0 1 4.864-12.288 17.408 17.408 0 0 1 24.064 0zM518.912 640a55.04 55.04 0 0 0 0-67.328 36.864 36.864 0 0 0-29.952-13.568 36.352 36.352 0 0 0-29.44 13.568 51.2 51.2 0 0 0-11.52 33.536 51.2 51.2 0 0 0 11.264 33.792 37.632 37.632 0 0 0 29.696 13.312 38.4 38.4 0 0 0 29.952-13.312zM712.448 539.136a70.4 70.4 0 0 1 25.6 27.648 86.016 86.016 0 0 1 9.216 40.192 87.04 87.04 0 0 1-9.216 40.448 68.608 68.608 0 0 1-25.6 27.904 61.696 61.696 0 0 1-34.304 9.984 57.6 57.6 0 0 1-25.6-5.632 61.952 61.952 0 0 1-19.712-13.312v1.792a16.64 16.64 0 0 1-16.896 17.152 15.616 15.616 0 0 1-12.032-4.864 16.128 16.128 0 0 1-4.864-12.288v-176.64a16.64 16.64 0 0 1 4.864-12.288 17.408 17.408 0 0 1 24.064 0 16.64 16.64 0 0 1 4.864 12.288v58.624a55.296 55.296 0 0 1 18.688-14.592 53.248 53.248 0 0 1 25.6-6.4 64.512 64.512 0 0 1 35.328 9.984z m-9.984 102.4a51.2 51.2 0 0 0 11.52-33.792 51.2 51.2 0 0 0-11.264-33.536 40.192 40.192 0 0 0-59.648 0 55.04 55.04 0 0 0 0 67.328 37.632 37.632 0 0 0 29.952 13.568 37.12 37.12 0 0 0 29.44-15.104zM857.088 337.408V281.6a76.8 76.8 0 0 0-76.8-76.8H402.176a76.8 76.8 0 0 0-57.088-25.6H206.336a76.8 76.8 0 0 0-76.8 76.8v512a76.8 76.8 0 0 0 76.8 76.8h622.592a76.8 76.8 0 0 0 76.8-76.8V409.6a76.8 76.8 0 0 0-48.64-72.192z m-51.2-56.576v51.2h-153.6V281.6a76.8 76.8 0 0 0-4.608-25.6h132.608a25.6 25.6 0 0 1 25.6 25.6z m-230.4-25.6a25.6 25.6 0 0 1 25.6 25.6v51.2h-179.2V256z m279.04 512a25.6 25.6 0 0 1-25.6 25.6H206.336a25.6 25.6 0 0 1-25.6-25.6V256a25.6 25.6 0 0 1 25.6-25.6h138.752a25.6 25.6 0 0 1 25.6 25.6v102.4a25.6 25.6 0 0 0 25.6 25.6h432.64a25.6 25.6 0 0 1 25.6 25.6z",
            fill: icon_color
        }));
        icon.featured = el("svg", {
            viewBox: "0 0 1024 1024",
            className: "svg-icon",
            xmlns: xmlns,
        }, el("path", {
            d: "M923.36 854.72c37.92 0 68.64 30.72 68.64 68.64S961.28 992 923.36 992H512C248 992 32 776 32 512S248 32 512 32s480 216 480 480c0 133.92-54.72 257.28-144 342.72h75.36zM512 820.64c58.08 0 102.72-44.64 102.72-102.72S570.08 615.2 512 615.2s-102.72 44.64-102.72 102.72S453.92 820.64 512 820.64zM306.08 409.28c-58.08 0-102.72 44.64-102.72 102.72S248 614.72 306.08 614.72 408.8 570.08 408.8 512s-44.16-102.72-102.72-102.72zM512 203.36c-58.08 0-102.72 44.64-102.72 102.72S453.92 408.8 512 408.8s102.72-44.64 102.72-102.72S570.08 203.36 512 203.36z m205.92 205.92c-58.08 0-102.72 44.64-102.72 102.72s44.64 102.72 102.72 102.72S820.64 570.08 820.64 512 776 409.28 717.92 409.28z",
            fill: icon_color
        }));

        var colors = [{
                color: '#fb2121'
            },
            {
                color: '#ef0c7e'
            },
            {
                color: '#F3AC07'
            },
            {
                color: '#8CA803'
            },
            {
                color: '#64BD05'
            },
            {
                color: '#11C33F'
            },
            {
                color: '#08B89A'
            },
            {
                color: '#09ACE2'
            },
            {
                color: '#1F91F3'
            },
            {
                color: '#3B6ED5'
            },
            {
                color: '#664FFA'
            },
            {
                color: '#A845F7'
            },
            {
                color: '#333'
            },
            {
                color: '#666'
            },
            {
                color: '#999'
            },
            {
                color: '#f8f8f8'
            },
        ];

        function _toConsumableArray(arr) {
            if (Array.isArray(arr)) {
                for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
                    arr2[i] = arr[i];
                }
                return arr2;
            } else {
                return Array.from(arr);
            }
        }

        var help_link = function (link, text) {
            text = text || '查看官方教程';
            link = (link.indexOf("http") != -1) ? link : "https://www.zibll.com/?s=" + link;
            return el('div', {
                    className: "padding-15",
                },
                el('a', {
                    className: "but jb-green em09",
                    href: link,
                    target: 'blank'
                }, text)
            );
        }

        var notices = function (type, txte, data, isDismissible) {
            type = type || 'info';
            wp.data.dispatch('core/notices').createNotice(
                type, // Can be one of: success, info, warning, error.
                txte, // Text string to display.
                data
            );
        }
        //--------------------------------------------------------------
        rB('zibllblock/tabs', {
            title: 'Zibll:Tab栏目',
            icon: icon.tabs,
            supports: {
                className: false, //保存时候不自动添加根class
                anchor: true, //允许添加描点
            },
            description: '在文章中添加多栏目的Tab',
            category: 'zibll_block_cat',
            attributes: {
                tabHeaders: {
                    type: 'array',
                    default: ['栏目 1', '栏目 2', '栏目 3']
                },
                tabActive: {
                    type: 'number',
                    default: 0
                },
                open: {
                    type: "number",
                    default: 0
                },
                layout: {
                    type: "string",
                    default: 'nav-top'
                },
            },

            edit: function (props, props2) {
                var at = props.attributes,
                    isS = props.isSelected,
                    sa = props.setAttributes,
                    tabHeaders = at.tabHeaders,
                    tabActive = at.tabActive,
                    open = at.open,
                    layout = at.layout;

                var clientId = props.clientId;


                var get_el = function (_key) {
                    return el('div', {
                            className: "tab-header-item" + (_key == tabActive ? ' active' : ''),
                            onClick: function () {
                                active(_key);
                            },
                        },
                        el(
                            RichText, {
                                tagName: "p",
                                className: "tab-header-input",
                                onChange: function (e) {
                                    tabHeaders[_key] = e;
                                    sa({
                                        tabHeaders: _toConsumableArray(tabHeaders),
                                    })
                                },
                                value: tabHeaders[_key],
                                placeholder: '输入标题...'
                            }),
                        el('div', {
                                className: "tab-operation"
                            },
                            el('a', {
                                className: "but b-red circular",
                                onClick: function () {
                                    remove(_key);
                                }
                            }, el('i', {
                                className: "fa fa-times",
                            }))
                        ),
                    );

                };


                var active = function (_key) {
                    var block = getBlock(clientId);
                    sa({
                        tabActive: _key
                    });

                    times(block.innerBlocks.length, function (n) {
                        updateBlockAttributes(block.innerBlocks[n].clientId, {
                            tabActive: _key,
                            id: n
                        });
                    });
                }

                var add = function () {
                    var tabItemBlock = createBlock('zibllblock/tab');
                    insertBlock(tabItemBlock, tabHeaders.length, clientId);
                    sa({
                        tabHeaders: [].concat(_toConsumableArray(tabHeaders), ''),
                    })
                    active(tabHeaders.length);
                }
                var remove = function (_key) {
                    if (tabHeaders.length < 2) {
                        return notices('warning', '不能再删除！至少保留一个栏目');
                    }
                    var childBlocks = getBlockOrder(clientId);
                    removeBlock(childBlocks[_key], false);
                    tabHeaders.splice(_key, 1);
                    sa({
                        tabHeaders: _toConsumableArray(tabHeaders),
                    })
                    return active(0), !1;
                }

                return el('div', {
                        className: "tab-card theme-card-box " + layout,
                    },
                    el('div', {
                            className: "tab-header flex ab"
                        },
                        tabHeaders.map(function (item, index) {
                            return get_el(index);
                        }),
                        [isS && el('a', {
                            className: "but jb-blue em09 ml6",
                            onClick: add
                        }, "+ 添加"), ],

                    ),
                    el('div', {
                            className: "tab-content"
                        },
                        el(InnerBlocks, {
                            template: [
                                ['zibllblock/tab', {
                                    id: 0
                                }],
                                ['zibllblock/tab', {
                                    id: 1
                                }],
                                ['zibllblock/tab', {
                                    id: 2
                                }]
                            ],
                            templateLock: false,
                            allowedBlocks: ['zibllblock/tab']
                        })
                    ),
                    el(InspectorControls, null,
                        help_link('TAB栏目'),
                        el(PanelBody, {
                                icon: "admin-generic",
                                title: "设置"
                            },
                            el('div', {
                                    className: "components-base-control",
                                },
                                //侧边设置
                                el(SelectControl, {
                                    label: "导航栏位置",
                                    value: layout,
                                    onChange: function (e) {
                                        sa({
                                            layout: e
                                        })
                                    },
                                    options: [{
                                        label: '顶部',
                                        value: 'nav-top'
                                    }, {
                                        label: '左侧',
                                        value: 'nav-left'
                                    }, {
                                        label: '右侧',
                                        value: 'nav-left nav-right'
                                    }],
                                }),
                            ))
                    )

                )
            },
            save: function (props) {
                var at = props.attributes,
                    tabHeaders = at.tabHeaders,
                    open = at.open,
                    layout = at.layout;

                var header = function (_key) {
                    return el('li', {
                        className: (_key == open ? 'active' : ''),
                    }, el('a', {
                        className: 'post-tab-toggle',
                        'href': 'javascript:;',
                        'tab-id': _key,
                    }, tabHeaders[_key] || '栏目' + (_key + 1)))
                };

                return el('div', {
                    className: 'mb20 post-tab ' + layout,
                }, el('div', {
                        className: 'list-inline scroll-x mini-scrollbar tab-nav-theme'
                    },
                    tabHeaders.map(function (item, index) {
                        return header(index);
                    }),
                ), el('div', {
                        className: 'tab-content'
                    },
                    el(InnerBlocks.Content, null),
                ))
            },
        });

        //--------------------------------------------------------------
        rB('zibllblock/tab', {
            title: 'Zibll:Tab栏目',
            parent: ['zibllblock/tab'], //父级快
            icon: icon.tabs,
            supports: {
                className: false, //保存时候不自动添加根class
                reusable: false
            },
            category: 'zibll_block_cat',
            attributes: {
                id: {
                    type: 'number',
                    default: 0
                },
                pid: {
                    type: 'string'
                },
                tabActive: {
                    type: 'number',
                    default: 0
                },
                open: {
                    type: 'number',
                    default: 0
                },
            },

            edit: function (props) {
                var at = props.attributes,
                    isS = props.isSelected,
                    sa = props.setAttributes,
                    id = at.id,
                    tabActive = at.tabActive,
                    pid = at.pid;

                return el(
                    Fragment,
                    null,
                    el(
                        "div", {
                            className: '',
                            style: {
                                display: id === tabActive ? 'block' : 'none'
                            }
                        },
                        el(InnerBlocks, {
                            template: [
                                ['core/paragraph']
                            ],
                            templateLock: false
                        })
                    )
                );
            },
            save: function (props) {
                var at = props.attributes,
                    id = at.id,
                    open = at.open;

                return el('div', {
                    className: 'tab-pane fade' + (id == open ? ' active in' : ''),
                    'tab-id': id,
                }, el(InnerBlocks.Content, null))
            },
        });


        //--------------------------------------------------------------
        rB('zibllblock/dplayerfeatured', {
            title: 'Zibll:视频剧集',
            icon: icon.featured,
            supports: {
                className: false, //保存时候不自动添加根class
                color: false, //为块添加背景和颜色设置
            },
            description: '在文章中插入多剧集的视频，支持本地视频以及m3u8、mpd、flv等流媒体格式',
            category: 'zibll_block_cat',
            attributes: {
                featured: {
                    type: 'array',
                    source: 'query',
                    selector: '.switch-video',
                    default: [{
                        text: "",
                        url: "",
                        media_id: "",
                    }, {
                        text: "",
                        url: "",
                        media_id: "",
                    }],
                    query: {
                        text: {
                            type: 'string',
                            source: 'attribute',
                            attribute: 'title',
                        },
                        url: {
                            type: 'string',
                            source: 'attribute',
                            attribute: 'video-url',
                        },
                        media_id: {
                            type: 'number',
                            source: 'attribute',
                            attribute: 'media-id',
                        },
                    }
                },
                pic: {
                    type: "string",
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'video-pic',
                    default: ""
                },
                loop: {
                    type: "string",
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-loop',
                    default: ''
                },
                autoplay: {
                    type: "string",
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-autoplay',
                    default: ''
                },
                volume: {
                    type: "string",
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-volume',
                    default: 1
                },
                height: {
                    type: "string",
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-scale-height',
                    default: 0
                },
                hide_controller: {
                    type: "boolean",
                    default: false
                },
            },
            transforms: {
                to: [{
                    type: "block",
                    blocks: ["zibllblock/dplayer"],
                    transform: function (e) {
                        var attr = {
                            url: e.featured[0].url,
                            media_id: e.featured[0].media_id,
                            pic: e.pic,
                            loop: e.loop,
                            autoplay: e.autoplay,
                            volume: e.volume,
                            hide_controller: e.hide_controller,
                        };
                        return createBlock("zibllblock/dplayer",
                            attr
                        )
                    }
                }]
            },
            edit: function (props) {
                var at = props.attributes,
                    isS = props.isSelected,
                    sa = props.setAttributes,
                    featured = at.featured,
                    pic = at.pic,
                    loop = at.loop,
                    autoplay = at.autoplay,
                    hide_controller = at.hide_controller,
                    height = at.height || 0,
                    volume = at.volume || 1;

                var get_el = function (_key, _val) {
                    return el('div', {
                            className: "flex featured-item",
                        },
                        el('span', {
                            className: "mr10 c-red",
                        }, (_key + 1)),
                        el('div', {
                                className: "flex1",
                            },
                            el('div', {
                                className: "flex ab",
                            }, el(
                                TextControl, {
                                    className: "flex1 mr6",
                                    tagName: 'input',
                                    onChange: function (e) {
                                        featured[_key].text = e;
                                        sa({
                                            featured: _toConsumableArray(featured),
                                        })
                                    },
                                    value: featured[_key].text,
                                    placeholder: '请输入剧集标题，默认为：第' + (_key + 1) + '集'
                                }), [featured.length > 2 && el(Button, {
                                className: "but c-red but-icon",
                                onClick: function () {
                                    remove(_key);
                                }
                            }, el('i', {
                                className: "fa fa-times",
                            }))], ),
                            el('div', {
                                    className: "flex ab",
                                }, el(
                                    TextControl, {
                                        className: "flex1 mr6",
                                        tagName: 'input',
                                        onChange: function (e) {
                                            featured[_key].url = e;
                                            featured[_key].media_id = '';
                                            sa({
                                                featured: _toConsumableArray(featured),
                                            })
                                        },
                                        value: featured[_key].url,
                                        placeholder: '输入视频地址或选择、上传本地视频'
                                    }),
                                el(MediaUpload, {
                                    title: "选择或上传视频",
                                    allowedTypes: ["video"],
                                    onSelect: function onSelect(media) {
                                        featured[_key].url = media.url;
                                        featured[_key].media_id = media.id;
                                        sa({
                                            featured: _toConsumableArray(featured),
                                        })
                                    },
                                    value: featured[_key].media_id,
                                    render: function render(_ref) {
                                        var open = _ref.open;
                                        return el('a', {
                                            className: "but c-blue is-secondary flex0",
                                            onClick: open
                                        }, "本地视频");
                                    }
                                }))),
                    );
                };

                var add = function () {
                    let add = {
                        url: "",
                        media_id: "",
                    }
                    sa({
                        featured: [].concat(_toConsumableArray(featured), add),
                    })
                }
                var remove = function (_key) {
                    if (featured.length < 3) {
                        return notices('warning', '不能再删除！至少保留两个剧集');
                    }
                    featured.splice(_key, 1);
                    sa({
                        featured: _toConsumableArray(featured),
                    })
                }

                return el('div', {}, el('div', {
                            className: "theme-card-box"
                        },
                        el('div', {
                                className: "em12 mb10 flex ac",
                            }, icon.featured,
                            el('span', {
                                className: "ml10",
                            }, 'Zibll剧集视频模块'), ),
                        el('div', {
                            className: "opacity8 mb10 em09",
                        }, '在文章中插入多剧集的视频，支持本地视频以及m3u8、mpd、flv等流媒体格式，如果仅需单个视频请使用“Zibll视频”模块'),

                        [isS ? featured.map(function (item, index) {
                            return get_el(index);
                        }) : featured.map(function (item, index) {
                            return el('div', {
                                className: "flex featured-item px12 opacity8",
                                style: {
                                    'padding': '2px 10px',
                                    'margin-bottom': '-1px',
                                },
                            }, (index + 1) + '：' + item.url);
                        })],
                        [isS && el('a', {
                            className: "but jb-blue",
                            onClick: add
                        }, "添加剧集"), ],
                    ),

                    el(InspectorControls, null,
                        help_link('剧集视频'),
                        el(PanelBody, {
                                icon: "admin-generic",
                                title: "设置"
                            },
                            el('div', {
                                    className: "components-base-control",
                                },
                                [pic && el('div', {
                                    className: " ",
                                }, el('img', {
                                    src: pic,
                                }, ))],

                                el(MediaUpload, {
                                    title: "选择或上传视频海报图像",
                                    allowedTypes: ["image"],
                                    onSelect: function onSelect(media) {
                                        sa({
                                            pic: media.url,
                                        });
                                    },
                                    render: function render(_ref) {
                                        var open = _ref.open;
                                        return el('a', {
                                            className: "but jb-blue",
                                            onClick: open
                                        }, (pic ? '替换' : '添加') + "海报图像");
                                    }
                                }),
                            ),

                            el(ToggleControl, {
                                label: '自动播放',
                                checked: autoplay,
                                onChange: function (e) {
                                    sa({
                                        autoplay: e
                                    })
                                },
                            }),
                            el(ToggleControl, {
                                label: '循环播放',
                                checked: loop,
                                onChange: function (e) {
                                    sa({
                                        loop: e
                                    })
                                },
                            }),
                            el(ToggleControl, {
                                label: '隐藏进度条及播放控件',
                                checked: hide_controller,
                                onChange: function (e) {
                                    sa({
                                        hide_controller: e
                                    })
                                },
                            }),
                            el(RangeControl, {
                                label: "初始音量",
                                value: ~~(volume * 100),
                                onChange: function (e) {
                                    sa({
                                        volume: e / 100
                                    })
                                },
                                allowReset: true,
                                min: "0",
                                max: "100"
                            }),
                            el(RangeControl, {
                                label: "固定长宽比例",
                                help: (height > 0 ? '高度为宽度的' + height + '%' : '为0则为不固定，长宽比例自动与视频比例同步'),
                                value: ~~height,
                                onChange: function (e) {
                                    sa({
                                        height: e
                                    })
                                },
                                allowReset: true,
                                min: "0",
                                step: '5',
                                max: "200"
                            }),
                        )
                    )

                )
            },
            save: function (props) {
                var at = props.attributes,
                    pic = at.pic,
                    loop = at.loop || '',
                    autoplay = at.autoplay || '',
                    hide_controller = at.hide_controller || '',
                    height = at.height || 0,
                    featured = at.featured,
                    volume = at.volume || 1;
                if (!featured || !featured[0]) return;
                var url = featured[0].url;

                var graphic = el('div', {
                    className: "graphic",
                    style: {
                        'padding-bottom': '50%',
                    },
                }, el('div', {
                    className: "abs-center text-center",
                }, el('i', {
                    className: "fa fa-play-circle fa-4x muted-3-color opacity5",
                })))

                var attr = {
                    className: "new-dplayer" + (hide_controller ? ' controller-hide' : '') + (height > 0 ? ' dplayer-scale-height' : ''),
                    'video-url': url,
                    'video-pic': pic,
                    'data-loop': loop,
                    'data-autoplay': autoplay,
                    'data-volume': volume,
                };
                if (height > 0) {
                    attr.style = '--scale-height:' + height + '%;';
                    attr['data-scale-height'] = height;
                }

                return el('div', {
                        className: 'mb20'
                    },
                    el('div', attr, graphic), el('div', {
                        className: 'featured-video-episode mt10 dplayer-featured',
                    }, featured.map(function (item, index) {
                        index = index + 1;
                        var text = item.text || '第' + index + '集';
                        // if (!item.url) return;
                        return el('a', {
                            className: 'switch-video text-ellipsis' + (index == 1 ? ' active' : ''),
                            'data-index': index,
                            'video-url': item.url,
                            'media-id': item.media_id,
                            'title': item.text,
                            'href': 'javascript:;',
                        }, el('span', {
                            className: 'mr6 badg badg-sm',
                        }, index), el('i', {
                            className: 'episode-active-icon',
                        }, ''), text);
                    })))
            },
        });

        //--------------------------------------------------------------------
        rB('zibllblock/iframe', {
            title: 'Zibll:超级嵌入',
            icon: icon.iframe,
            description: '在文章中嵌入其他在线内容，通常用于嵌入其它网站的视频播放器或音乐播放器，也可以嵌入其它任意在线内容',
            category: 'zibll_block_cat',
            attributes: {
                url: {
                    source: 'attribute',
                    selector: 'iframe',
                    attribute: 'src'
                },
                aspect: {
                    type: "attribute",
                    selector: "iframe",
                    attribute: 'data-aspect'
                },
                allowfullscreen: {
                    type: "attribute",
                    selector: "iframe",
                    attribute: 'allowfullscreen'
                },
            },
            edit: function (props) {
                var at = props.attributes,
                    isS = props.isSelected,
                    sa = props.setAttributes,
                    url = at.url,
                    allowfullscreen = at.allowfullscreen,
                    aspect = at.aspect || '56%';

                return el('div', {
                        className: 'zibllblock-iframe'
                    }, el('div', {
                            className: ((!url || isS) ? "theme-card-box" : "nois_selected")
                        },
                        [url && el('div', {
                                className: "zibllblock-iframe-div",
                                style: {
                                    'padding-bottom': aspect,
                                },
                            }, el('iframe', {
                                className: "",
                                width: '100%',
                                src: url,
                            }),
                            el('div', {
                                className: "iframe-mask",
                            }))],
                        [!url && el('div', {
                            className: "components-placeholder__label",
                            style: {
                                width: '95%',
                                height: '27px',
                                'font-size': '15px',
                            },
                        }, icon.iframe, '嵌入在线内容'), ],
                        [(!url || isS) && el(
                            'div', {
                                className: "mt20",
                            }, el(
                                'div', {
                                    className: "iframe-label",
                                }, '请输入需要嵌入的链接，或者直接粘贴iframe嵌入代码以自动提取链接'),
                            el(
                                'a', {
                                    className: "block-editor-block-card__description",
                                }, ''),

                        )],
                        [(!url || isS) && el(
                            TextControl, {
                                style: {
                                    width: '95%',
                                },
                                tagName: 'input',
                                onChange: function (e) {
                                    var html = $.parseHTML(e);
                                    var src = $(html).attr('src') || e;
                                    sa({
                                        url: src,
                                    })
                                },
                                value: url,
                                placeholder: '请输入链接或粘贴嵌入代码'
                            })]
                    ),

                    el(InspectorControls, null,
                        help_link('超级嵌入'),
                        el(PanelBody, {
                                icon: "admin-generic",
                                title: "设置"
                            },
                            el(SelectControl, {
                                label: "长宽比例设置",
                                value: aspect,
                                onChange: function (e) {
                                    sa({
                                        aspect: e
                                    })
                                },
                                options: [{
                                    label: '横版-4:1',
                                    value: '25%'
                                }, {
                                    label: '横版-3:1',
                                    value: '33%'
                                }, {
                                    label: '横版-5:2',
                                    value: '40%'
                                }, {
                                    label: '横版-2:1',
                                    value: '50%'
                                }, {
                                    label: '横版-16:9',
                                    value: '56%'
                                }, {
                                    label: '横版-5:3',
                                    value: '60%'
                                }, {
                                    label: '横版-4:3',
                                    value: '75%'
                                }, {
                                    label: '横版-5:4',
                                    value: '80%'
                                }, {
                                    label: '横版-8:7',
                                    value: '87.5%'
                                }, {
                                    label: '正方形-1:1',
                                    value: '100%'
                                }, {
                                    label: '竖版-7:8',
                                    value: '114%'
                                }, {
                                    label: '竖版-4:5',
                                    value: '125%'
                                }, {
                                    label: '竖版-3:4',
                                    value: '133%'
                                }, {
                                    label: '竖版-3:5',
                                    value: '166%'
                                }, {
                                    label: '竖版-1:2',
                                    value: '200%'
                                }, {
                                    label: '竖版-2:5',
                                    value: '250%'
                                }, {
                                    label: '竖版-1:3',
                                    value: '300%'
                                }],
                            }),
                            el(ToggleControl, {
                                label: '允许内容全屏',
                                checked: !!allowfullscreen,
                                onChange: function (e) {
                                    sa({
                                        allowfullscreen: e
                                    })
                                },
                            }),
                        )
                    )

                )
            },
            save: function (props) {
                var at = props.attributes,
                    url = at.url,
                    allowfullscreen = at.allowfullscreen,
                    aspect = at.aspect || '56%';
                var iframe_attr = {
                    className: "",
                    'data-aspect': aspect,
                    'framespacing': '0',
                    'border': '0',
                    width: '100%',
                    'frameborder': 'no',
                    src: url,
                };
                if (at.allowfullscreen) {
                    iframe_attr.allowfullscreen = 'allowfullscreen';
                }

                if (!url) return;

                return el('div', {
                    className: "wp-block-embed is-type-video mb20",
                }, el('div', {
                    className: "",
                    style: {
                        'padding-bottom': aspect,
                    },
                }, el('iframe', iframe_attr)))
            },
        });


        /////////------------------------------------------------------------

        rB('zibllblock/dplayer', {
            title: 'Zibll:视频',
            icon: icon.video,
            description: '在文章中插入视频，支持本地视频以及m3u8、mpd、flv等流媒体格式',
            category: 'zibll_block_cat',
            attributes: {
                url: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'video-url'
                },
                media_id: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'media-id'
                },
                pic: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'video-pic'
                },
                loop: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-loop'
                },
                autoplay: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-autoplay'
                },
                volume: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-volume'
                },
                height: {
                    type: "string",
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-scale-height',
                    default: 0
                },
                hide_controller: {
                    source: 'attribute',
                    selector: '.new-dplayer',
                    attribute: 'data-hide'
                },
            },
            transforms: {
                to: [{
                    type: "block",
                    blocks: ["zibllblock/dplayerfeatured"],
                    transform: function (e) {
                        var attr = {
                            featured: [{
                                text: "",
                                url: e.url,
                                media_id: e.media_id,
                            }, {
                                text: "",
                                url: "",
                                media_id: "",
                            }],
                            pic: e.pic,
                            loop: e.loop,
                            autoplay: e.autoplay,
                            volume: e.volume,
                            hide_controller: e.hide_controller,
                        };
                        return createBlock("zibllblock/dplayerfeatured",
                            attr
                        )
                    }
                }],
            },
            edit: function (props) {
                var at = props.attributes,
                    isS = props.isSelected,
                    sa = props.setAttributes,
                    url = at.url,
                    media_id = at.media_id,
                    pic = at.pic,
                    loop = at.loop,
                    autoplay = at.autoplay,
                    hide_controller = at.hide_controller,
                    height = at.height || 0,
                    volume = at.volume || 1;

                return el('div', {}, el('div', {
                            className: "theme-card-box"
                        },
                        el('div', {
                                className: "em12 mb10 flex ac",
                            }, icon.video,
                            el('span', {
                                className: "ml10",
                            }, 'Zibll视频模块'), ),
                        el('div', {
                            className: "opacity8 mb10 em09",
                        }, '支持本地视频以及m3u8、mpd、flv等流媒体格式，如需插入多剧集的视频请使用“Zibll剧集视频”模块'),

                        el(
                            TextControl, {
                                style: {
                                    width: '95%',
                                },
                                tagName: 'input',
                                onChange: function (e) {
                                    sa({
                                        url: e,
                                        media_id: ''
                                    })
                                },
                                value: url,
                                placeholder: '输入视频地址或选择、上传本地视频'
                            }),

                        el(MediaUpload, {
                            title: "选择或上传视频",
                            allowedTypes: ["video"],
                            onSelect: function onSelect(media) {
                                sa({
                                    url: media.url,
                                    media_id: media.id
                                });
                            },
                            value: media_id,
                            render: function render(_ref) {
                                var open = _ref.open;
                                return isS && el('a', {
                                    className: "but jb-blue",
                                    onClick: open
                                }, "选择本地视频");
                            }
                        })
                    ),

                    el(InspectorControls, null,
                        help_link('视频教程'),
                        el(PanelBody, {
                                icon: "admin-generic",
                                title: "设置"
                            },
                            el('div', {
                                    className: "components-base-control",
                                },
                                [pic && el('div', {
                                    className: " ",
                                }, el('img', {
                                    src: pic,
                                }, ))],

                                el(MediaUpload, {
                                    title: "选择或上传视频海报图像",
                                    allowedTypes: ["image"],
                                    onSelect: function onSelect(media) {
                                        sa({
                                            pic: media.url,
                                        });
                                    },
                                    render: function render(_ref) {
                                        var open = _ref.open;
                                        return el(Button, {
                                            className: "but jb-blue is-secondary",
                                            onClick: open
                                        }, (pic ? '替换' : '添加') + "海报图像");
                                    }
                                }),
                            ),

                            el(ToggleControl, {
                                label: '自动播放',
                                checked: autoplay,
                                onChange: function (e) {
                                    sa({
                                        autoplay: e
                                    })
                                },
                            }),
                            el(ToggleControl, {
                                label: '循环播放',
                                checked: loop,
                                onChange: function (e) {
                                    sa({
                                        loop: e
                                    })
                                },
                            }),
                            el(ToggleControl, {
                                label: '隐藏进度条及播放控件',
                                checked: hide_controller,
                                onChange: function (e) {
                                    sa({
                                        hide_controller: e
                                    })
                                },
                            }),
                            el(RangeControl, {
                                label: "初始音量",
                                value: ~~(volume * 100),
                                onChange: function (e) {
                                    sa({
                                        volume: e / 100
                                    })
                                },
                                allowReset: true,
                                min: "0",
                                max: "100"
                            }),
                            el(RangeControl, {
                                label: "固定长宽比例",
                                help: (height > 0 ? '高度为宽度的' + height + '%' : '为0则为不固定，长宽比例自动与视频比例同步'),
                                value: ~~height,
                                onChange: function (e) {
                                    sa({
                                        height: e
                                    })
                                },
                                allowReset: true,
                                min: "0",
                                step: '5',
                                max: "200"
                            }),
                        )
                    )

                )
            },
            save: function (props) {
                var at = props.attributes,
                    url = at.url,
                    media_id = at.media_id,
                    pic = at.pic,
                    loop = at.loop || '',
                    autoplay = at.autoplay || '',
                    hide_controller = at.hide_controller || '',
                    height = at.height || 0,
                    volume = at.volume || 1;
                if (!url) return;

                var graphic = el('div', {
                    className: "graphic",
                    style: {
                        'padding-bottom': '50%',
                    },
                }, el('div', {
                    className: "abs-center text-center",
                }, el('i', {
                    className: "fa fa-play-circle fa-4x muted-3-color opacity5",
                })))
                var attr = {
                    className: "new-dplayer post-dplayer" + (hide_controller ? ' controller-hide' : '') + (height > 0 ? ' dplayer-scale-height' : ''),
                    'video-url': url,
                    'media-id': media_id,
                    'video-pic': pic,
                    'data-loop': loop,
                    'data-hide': hide_controller,
                    'data-autoplay': autoplay,
                    'data-volume': volume,
                };
                if (height > 0) {
                    attr.style = '--scale-height:' + height + '%;';
                    attr['data-scale-height'] = height;
                }
                return el('div', attr, graphic)
            },
        });

        //---------------------------------------------
        rB('zibllblock/feature', {
            title: 'Zibll:亮点',
            icon: icon.feature,
            description: '包含图标和简介的亮点介绍，建议4个一组',
            category: 'zibll_block_cat',
            attributes: {
                title: {
                    type: "array",
                    source: 'children',
                    selector: ".feature-title",
                },
                icon: {
                    source: 'attribute',
                    selector: '.feature',
                    attribute: 'data-icon'
                },
                note: {
                    type: "array",
                    source: 'children',
                    selector: ".feature-note",
                },
                theme: {
                    source: 'attribute',
                    selector: '.feature',
                    attribute: 'class'
                },
                color: {
                    source: 'attribute',
                    selector: '.feature',
                    attribute: 'data-color'
                },
            },
            edit: function (props) {
                var at = props.attributes,
                    isS = props.isSelected,
                    sa = props.setAttributes,
                    bt = at.title,
                    jj = at.note,
                    ic = at.icon || 'fa-flag',
                    icc = at.color || 'feature-icon no1',
                    zt = at.theme || 'panel panel-default';

                function changecolor(e) {
                    var type = e.target.className;
                    sa({
                        color: type
                    });
                }

                function changetheme(e) {
                    var type = e.target.className;
                    sa({
                        theme: type
                    });
                }

                let Coc = el(c.ColorPalette, {
                    value: icc || '#444',
                    colors: colors,
                    className: "q-ColorPalette",
                    onChange: function (e) {
                        sa({
                            color: e
                        })
                    }
                });

                rti = el(
                        TextControl, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    icon: e
                                })
                            },
                            value: ic,
                            placeholder: '请输入图标class代码...'
                        }),

                    rt = el(
                        RichText, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    title: e
                                })
                            },
                            value: bt,
                            placeholder: '请输入亮点标题...'
                        }),
                    rtj = el(
                        RichText, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    note: e
                                })
                            },
                            value: jj,
                            placeholder: '请输入亮点简介...'
                        }),

                    ztan = el('div', {
                        className: 'g_extend anz panel_b'
                    }, [
                        el('button', {
                            className: 'feature-icon no1',
                            onClick: changecolor
                        }),
                        el('button', {
                            className: 'feature-icon no2',
                            onClick: changecolor
                        }),
                        el('button', {
                            className: 'feature-icon no3',
                            onClick: changecolor
                        }),
                        el('button', {
                            className: 'feature-icon no4',
                            onClick: changecolor
                        }),
                        el('button', {
                            className: 'feature-icon no5',
                            onClick: changecolor
                        }),
                    ]);

                return el('div', {}, el('div', {
                            className: "feature"
                        },
                        el('div', {
                            className: "feature-icon"
                        }, el('i', {
                            style: {
                                color: icc
                            },
                            className: "fa " + ic
                        })),

                        [isS && el('div', {
                            className: "feature-iconbj"
                        }, el('div', {
                            className: "feature-icont"
                        }, '请输入FA图标class代码：'), rti)], [isS && Coc],
                        el('div', {
                            className: "feature-title"
                        }, rt),
                        el('div', {
                            className: "feature-note"
                        }, rtj)
                    ),

                    el(InspectorControls, null,
                        el('div', {
                                className: 'modal-ss block-editor-block-card'
                            },
                            el('div', {
                                    className: "components-base-control",
                                },
                                el('h5', {}, '图标使用说明'),
                                el('p', {}, '图标使用Font Awesome图标库v4.7版本，你可以搜索Font Awesome或者在以下网站找到全部图标代码'),
                                el('a', {
                                    href: 'http://fontawesome.dashgame.com',
                                    target: 'blank'
                                }, 'Font Awesome图标库'), )
                        ),
                        el(PanelBody, {
                                icon: "admin-generic",
                                title: "设置"
                            },
                            el('div', {
                                    className: "components-base-control",
                                },
                                el('div', {
                                    className: "feature-icont"
                                }, 'FA图标class代码：'), rti,
                                el('div', {
                                    className: "feature-icont"
                                }, '选择图标颜色：'), Coc, )


                        )
                    )

                )
            },
            save: function (props) {
                var at = props.attributes,
                    bt = at.title,
                    jj = at.note,
                    icc = at.color || 'feature-icon no1',
                    ic = at.icon || 'fa-flag',
                    zt = at.theme || 'feature feature-default';

                return el('div', {
                        className: zt,
                        'data-icon': ic,
                        'data-color': icc
                    },
                    el('div', {
                        className: "feature-icon"
                    }, el('i', {
                        style: {
                            color: icc
                        },
                        className: "fa " + ic
                    })),
                    el('div', {
                        className: 'feature-title'
                    }, bt),
                    el('div', {
                        className: 'feature-note'
                    }, jj),
                );
            },
        });

        //---------------------------------------------
        rE('zibllblock/set-color', {
            title: '设定颜色',
            tagName: 'qc',
            className: null,
            attributes: {
                style: 'style',
                block: 'inline-block'
            },
            edit: class extend_Edit extends Component {
                constructor() {
                    super(...arguments);
                    this.show_modal = this.show_modal.bind(this);
                    this.close_modal = this.close_modal.bind(this);
                    this.words_selected = this.words_selected.bind(this);
                    this.set_popover_rect = this.set_popover_rect.bind(this);
                    this.remove_Format = this.remove_Format.bind(this);
                    this.onChange_cb = this.onChange_cb.bind(this);
                    this.set_color = this.set_color.bind(this);
                    this.set_color2 = this.set_color2.bind(this);
                    this.xsba = this.xsba.bind(this);
                    this.xsba_f = this.xsba_f.bind(this);
                    this.set_background = this.set_background.bind(this);
                    this.set_background2 = this.set_background2.bind(this);
                    this.state = {
                        modal_visibility: false
                    };
                }
                words_selected() {
                    const {
                        value
                    } = this.props;
                    return value.start !== value.end;
                }
                set_popover_rect() {
                    const selection = window.getSelection();
                    const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
                    var rectangle = getRectangleFromRange(range);
                    this.setState({
                        popover_rect: rectangle
                    });
                }
                show_modal() {
                    this.setState({
                        modal_visibility: true
                    });
                    this.set_popover_rect();
                }
                close_modal() {
                    this.setState({
                        modal_visibility: false
                    });
                }
                xsba() {
                    this.setState({
                        xsba: true
                    });
                }
                xsba_f() {
                    this.setState({
                        xsba: false
                    });
                }
                remove_Format(e) {
                    this.setState({
                        color: '',
                        background: ''
                    });
                    this.props.onChange(wp.richText.toggleFormat(
                        this.props.value, {
                            type: 'zibllblock/set-color',
                        }))
                }
                onChange_cb() {
                    this.props.onChange(wp.richText.applyFormat(
                        this.props.value, {
                            type: 'zibllblock/set-color',
                            attributes: {
                                style: "color:" + this.state.color + ";background:" + this.state.background,
                            }
                        }))
                }
                set_color(e) {
                    this.setState({
                        color: 'rgba(' + e.rgb.r + ',' + e.rgb.g + ',' + e.rgb.b + ',' + e.rgb.a + ')'
                    });
                    this.onChange_cb();
                }
                set_color2(e) {
                    this.setState({
                        color: e
                    });
                    this.props.onChange(wp.richText.applyFormat(
                        this.props.value, {
                            type: 'zibllblock/set-color',
                            attributes: {
                                style: "color:" + e + ";background:" + this.state.background,
                            }
                        }))
                }

                set_background2(e) {
                    this.setState({
                        background: e
                    });
                    this.props.onChange(wp.richText.applyFormat(
                        this.props.value, {
                            type: 'zibllblock/set-color',
                            attributes: {
                                style: "color:" + this.state.color + ";background:" + e,
                            }
                        }))
                }
                set_background(e) {
                    this.setState({
                        background: 'rgba(' + e.rgb.r + ',' + e.rgb.g + ',' + e.rgb.b + ',' + e.rgb.a + ')'
                    });
                    this.onChange_cb();
                }
                render() {
                    let {
                        isActive
                    } = this.props;
                    var props = this.props;
                    let Co = el(c.ColorPicker, {
                        color: this.state.color || '#444',
                        onChangeComplete: this.set_color
                    });
                    let Coc = el(c.ColorPalette, {
                        value: this.state.color || '#444',
                        colors: colors,
                        clearable: false,
                        className: "q-ColorPalette",
                        disableCustomColors: true,
                        onChange: this.set_color2
                    });
                    let Bob = el(c.ColorPalette, {
                        value: this.state.background || '#fff',
                        colors: colors,
                        clearable: false,
                        className: "q-ColorPalette",
                        disableCustomColors: true,
                        onChange: this.set_background2
                    });
                    let Cob = el(c.ColorPicker, {
                            color: this.state.background || '#fff',
                            onChangeComplete: this.set_background
                        }),
                        cz = el('button', {
                            className: 'remove-Format',
                            onClick: this.remove_Format
                        }, el('span', {
                            className: 'dashicons dashicons-image-rotate',
                        }));
                    return el(Fragment, null, el(RichTextToolbarButton, {
                        icon: "art",
                        title: "自定义颜色",
                        onClick: this.show_modal,
                        isActive: isActive,
                        isDisabled: !this.words_selected()
                    }), this.state.modal_visibility && el(Popover, {
                            anchorRect: this.state.popover_rect,
                            position: "bottom center",
                            className: "color_popover",
                            onFocusOutside: this.close_modal
                        },
                        el(c.ButtonGroup, {},
                            el('button', {
                                className: "btn btn-default " + (!this.state.xsba && "isS"),
                                onClick: this.xsba_f,
                            }, "颜色"),
                            el('button', {
                                className: "btn btn-default " + (this.state.xsba && "isS"),
                                onClick: this.xsba,
                            }, "背景色")),

                        cz,
                        !this.state.xsba && el("div", {

                        }, el("p", {}, "请选择文字颜色"), Coc, Co),
                        this.state.xsba && el("div", {

                        }, el("p", {}, "请选择背景颜色"), Bob, Cob)

                    ));
                }
            }
        });

        //---------------------------------------------
        rB('zibllblock/modal', {
            title: 'Zibll:模态框',
            icon: {
                src: 'feedback',
                foreground: icon_color
            },
            description: '一个弹出框、模态框，默认不会显示，通过按钮让它弹出',
            category: 'zibll_block_cat',
            attributes: {
                biaoti: {
                    type: "array",
                    source: 'children',
                    selector: ".modal-title",
                },
                kuandu: {
                    type: "attribute",
                    selector: ".modal-dialog",
                    attribute: "data-kd",
                },
                btn1: {
                    type: "array",
                    source: 'children',
                    selector: "button.btn1",
                },
                btn2: {
                    type: "array",
                    source: 'children',
                    selector: "button.btn2",
                },
                id: {
                    source: 'attribute',
                    selector: '.modal',
                    attribute: 'id'
                },
                btn1kg: {
                    type: "attribute",
                    attribute: "data-bkg1",
                    default: true
                },
                btn2kg: {
                    type: "attribute",
                    attribute: "data-bkg2",
                    default: true
                },
                btntm1: {
                    type: "attribute",
                    selector: "button.btn1",
                    attribute: 'class'
                },
                btntm2: {
                    type: "attribute",
                    selector: "button.btn2",
                    attribute: 'class'
                },
                oncopy: {
                    source: 'string'
                }
            },
            edit: function (props) {
                var at = props.attributes,
                    bt = at.biaoti,
                    btn1 = at.btn1,
                    btn2 = at.btn2,
                    btntm1 = at.btntm1 || 'btn1 but',
                    btntm2 = at.btntm2 || 'btn2 but c-blue',
                    bkg1 = at.btn1kg,
                    bkg2 = at.btn2kg,
                    isS = props.isSelected,
                    onC = at.oncopy,
                    kd = at.kuandu || '',
                    sa = props.setAttributes;

                var sjs = parseInt((Math.random() + 1) * Math.pow(10, 4));
                console.log(props.clientId.substring(props.clientId.length - 8));

                if (!at.id) {
                    sa({
                        id: "modal_" + sjs
                    })
                }

                function sabt1(e) {
                    var type = e.target.className;
                    sa({
                        btntm1: 'btn1 ' + type
                    });
                }

                function sabt2(e) {
                    var type = e.target.className;
                    sa({
                        btntm2: 'btn2 ' + type
                    });
                }
                var xzk = el(InnerBlocks, {}, ''),
                    rt = el(
                        RichText, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    biaoti: e
                                })
                            },
                            value: bt,
                            placeholder: '请输入标题...'
                        }),
                    b1rt = el(
                        RichText, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    btn1: e
                                })
                            },
                            value: btn1,
                            placeholder: '按钮1'
                        }),
                    b2rt = el(
                        RichText, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    btn2: e
                                })
                            },
                            value: btn2,
                            placeholder: '按钮2'
                        }),
                    ztan1 = el('span', {
                        className: 'modal-bu'
                    }, [
                        el('button', {
                            className: 'but',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but c-blue',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but c-red',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but c-yellow',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but c-green',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but c-purple',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but b-blue',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but b-red',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but b-yellow',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but b-green',
                            onClick: sabt1
                        }),
                        el('button', {
                            className: 'but b-purple',
                            onClick: sabt1
                        }),
                    ]),
                    ztan2 = el('span', {
                        className: 'modal-bu'
                    }, [
                        el('button', {
                            className: 'but',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but c-blue',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but c-red',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but c-yellow',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but c-green',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but c-purple',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but b-blue',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but b-red',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but b-yellow',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but b-green',
                            onClick: sabt2
                        }),
                        el('button', {
                            className: 'but b-purple',
                            onClick: sabt2
                        }),
                    ]);
                return el('div', {}, el('div', {
                            className: 'modal ' + kd,
                        },
                        el('div', {
                            className: "modal-title"
                        }, rt, el('button', {
                            className: "close"
                        }, '×')),
                        el('div', {
                            className: "modal-body"
                        }, xzk),
                        [(bkg1 || bkg2) && el('div', {
                            className: "modal-footer"
                        }, [bkg1 && el('span', {
                            className: btntm1
                        }, b1rt)], [bkg2 && el('span', {
                            className: btntm2
                        }, b2rt)])]
                    ),

                    el(InspectorControls, {},

                        el('div', {
                                className: 'modal-ss padding-h'
                            },
                            el('h5', {}, '使用教程'),
                            el('p', {}, '模态框在页面中默认不会显示，需要一个触发按钮，将以下代码复制后填入任意链接的url中即可触发此模态框的弹出'),
                            el('p', {
                                className: 'modal-code'
                            }, "javascript:void($('#" + at.id + "').modal('show'));"),
                            el('div', {
                                    className: 'Copy'
                                },
                                el(ClipboardButton, {
                                    text: "javascript:void($('#" + at.id + "').modal('show'));",
                                    className: 'Copy but jb-blue',
                                    onCopy: function (e) {
                                        sa({
                                            oncopy: true
                                        })
                                    },
                                    onFinishCopy: function (e) {
                                        sa({
                                            oncopy: false
                                        })
                                    },
                                }, onC ? "代码已复制" : "点击复制代码"))

                        ),
                        el(PanelBody, {
                                title: "模态框设置"
                            },
                            el(SelectControl, {
                                label: "宽度选择",
                                value: kd,
                                onChange: function (e) {
                                    sa({
                                        kuandu: e
                                    })
                                },
                                options: [{
                                    label: '默认中等宽度',
                                    value: ''
                                }, {
                                    label: '超小宽度',
                                    value: 'modal-sm'
                                }, {
                                    label: '小型宽度',
                                    value: 'modal-mini'
                                }, {
                                    label: '更大宽度',
                                    value: 'modal-lg'
                                }],
                            }),

                            el('p', {}, ' '),
                            el(ToggleControl, {
                                label: '开启按钮1',
                                checked: bkg1,
                                onChange: function (e) {
                                    sa({
                                        btn1kg: e
                                    })
                                },
                            }), [bkg1 && ztan1],

                            el(ToggleControl, {
                                label: '开启按钮2',
                                checked: bkg2,
                                onChange: function (e) {
                                    sa({
                                        btn2kg: e
                                    })
                                },
                            }), [bkg2 && ztan2],

                        )
                    )
                );
            },
            save: function (props) {
                var con = InnerBlocks.Content,
                    at = props.attributes,
                    btn1 = at.btn1,
                    btn2 = at.btn2,
                    btntm1 = at.btntm1 || 'btn1 but',
                    btntm2 = at.btntm2 || 'btn2 but c-blue',
                    bkg1 = at.btn1kg,
                    bkg2 = at.btn2kg,
                    kd = at.kuandu,
                    id = at.id,
                    bt = at.biaoti;

                bth = el('div', {
                    className: "modal-header"
                }, el('strong', {
                    className: "modal-title",
                }, bt), el('button', {
                    className: "close",
                    "data-dismiss": "modal",
                }, el('div', {
                    'data-class': "ic-close",
                    'data-svg': "close",
                    'data-viewbox': "0 0 1024 1024"
                }, '')));
                coh = el('div', {
                    className: "modal-body"
                }, el(InnerBlocks.Content));

                foo = [((bkg1 && btn1) || (bkg2 && btn2)) && el('div', {
                        className: "modal-footer"
                    },
                    [(bkg1 && btn1) && el('button', {
                        className: btntm1,
                    }, btn1)],
                    [(bkg2 && btn2) && el('button', {
                        className: btntm2,
                    }, btn2)]

                )];

                return el('div', {}, el('div', {
                    className: 'modal fade',
                    id: id,
                    "aria-hidden": "true",
                    "data-bkg1": bkg1,
                    "aria-bkg2": bkg2,
                    "role": "dialog",
                    "tabindex": "-1",
                }, el('div', {
                    className: 'modal-dialog ' + kd,
                    "data-kd": kd,
                }, el('div', {
                        className: 'modal-content',
                    },

                    bth, coh, foo))));
            },
        });
        //---------------------------------------------
        rB('zibllblock/collapse', {
            title: 'Zibll:折叠框',
            icon: {
                src: 'sort',
                foreground: icon_color
            },
            description: '手风琴折叠框，可以插入任意内容，点击标题可切换内容显示和隐藏',
            category: 'zibll_block_cat',
            attributes: {
                biaoti: {
                    type: "array",
                    source: 'children',
                    selector: ".biaoti",
                },
                isshow: {
                    type: "attribute",
                    selector: '.panel',
                    attribute: "data-isshow",
                    default: true
                },
                theme: {
                    source: 'attribute',
                    selector: '.panel',
                    attribute: 'class'
                },
                id: {
                    source: 'attribute',
                    selector: '.collapse',
                    attribute: 'id'
                },
                ffs: {
                    source: 'string',
                }
            },
            edit: function (props) {
                var at = props.attributes,
                    bt = at.biaoti,
                    zt = at.theme || 'panel panel-default',
                    kg = at.isshow,
                    isS = props.isSelected,
                    ffs = at.ffs || 'ffshow',
                    sa = props.setAttributes;

                var sjs = parseInt((Math.random() + 1) * Math.pow(10, 4));

                if (!at.id) {
                    sa({
                        id: "collapse_" + sjs
                    })
                }

                function ffshow(e) {
                    if (ffs == 'ffshow') {
                        sa({
                            ffs: 'ffhide'
                        });
                    } else {
                        sa({
                            ffs: 'ffshow'
                        });
                    }
                }

                function changeType(e) {
                    var type = e.target.className;
                    sa({
                        theme: 'panel ' + type
                    });
                }
                var xzk = el(InnerBlocks, {}, ''),
                    rt = el(
                        RichText, {
                            tagName: 'div',
                            onChange: function (e) {
                                sa({
                                    biaoti: e
                                })
                            },
                            value: bt,
                            isSelected: props.isSelected,
                            placeholder: '请输入折叠框标题...'
                        }),
                    ztan = el('span', {
                        className: 'g_extend anz panel_b'
                    }, [
                        el('button', {
                            className: 'panel-default',
                            onClick: changeType
                        }),
                        el('button', {
                            className: 'panel-info',
                            onClick: changeType
                        }),
                        el('button', {
                            className: 'panel-success',
                            onClick: changeType
                        }),
                        el('button', {
                            className: 'panel-warning',
                            onClick: changeType
                        }), el('button', {
                            className: 'panel-danger',
                            onClick: changeType
                        }),
                    ]);
                return el('div', {}, el('div', {
                        className: zt,
                    }, el('div', {
                        className: "panel-heading"
                    }, rt), el('span', {
                        className: ffs + " isshow dashicons dashicons-arrow-down-alt2",
                        onClick: ffshow
                    }), el('div', {
                        className: ffs + " panel-body"
                    }, xzk)),

                    el(InspectorControls, null,
                        el(PanelBody, {
                                icon: "admin-generic",
                                title: "设置"
                            }, el('p', {}, el(ToggleControl, {
                                label: '默认展开',
                                checked: kg,
                                onChange: function (e) {
                                    sa({
                                        isshow: e
                                    })
                                }
                            })),
                            el('i', {
                                className: '.components-base-control__help'
                            }, kg ? '默认为展开状态' : '默认为折叠状态')))
                );
            },
            save: function (props) {
                var con = InnerBlocks.Content,
                    at = props.attributes,
                    zt = at.theme || 'panel',
                    kg = at.isshow,
                    id = at.id,
                    bt = at.biaoti;

                bth = el('div', {
                    className: "panel-heading " + (kg ? '' : 'collapsed'),
                    href: "#" + id,
                    "data-toggle": "collapse",
                    "aria-controls": "collapseExample",
                }, el('i', {
                    className: "fa fa-plus"
                }), el('strong', {
                    className: "biaoti ",
                }, bt))
                coh = el('div', {
                    className: "collapse " + (kg ? 'in' : ''),
                    id: id,
                }, el('div', {
                    className: "panel-body"
                }, el(InnerBlocks.Content)));

                return el('div', {}, el('div', {
                    className: zt,
                    "data-theme": zt,
                    "data-isshow": kg,
                }, bth, coh));
            },
        });

        //-------------------------------------------------------------
        rB('zibllblock/enlighter', {
            title: 'Zibll:高亮代码',
            icon: icon.enlighter,
            category: 'zibll_block_cat',
            description: '输入代码，将自动高亮显示',
            keywords: ["code", "sourcecode", "代码"],
            attributes: {
                content: {
                    type: "string",
                    selector: "code.gl",
                    source: "text"
                },
                c_language: {
                    type: "attribute",
                    attribute: "data-enlighter-language",
                    default: "generic"
                },
                theme: {
                    type: "attribute",
                    attribute: "data-enlighter-theme",
                    default: ""
                },
                highlight: {
                    type: "attribute",
                    attribute: "data-enlighter-highlight",
                    default: ""
                },
                linenumbers: {
                    type: "attribute",
                    attribute: "data-enlighter-linenumbers",
                    default: ""
                },
                lineoffset: {
                    type: "attribute",
                    attribute: "data-enlighter-lineoffset",
                    default: ""
                },
                title: {
                    type: "attribute",
                    attribute: "data-enlighter-title",
                    default: ""
                },
                group: {
                    type: "attribute",
                    attribute: "data-enlighter-group",
                    default: ""
                }
            },
            transforms: {
                from: [{
                    type: "raw",
                    priority: 4,
                    isMatch: function (e) {
                        return "PRE" === e.nodeName && 1 === e.children.length && "CODE" === e.firstChild.nodeName
                    },
                    transform: function (e) {
                        return createBlock("zibllblock/enlighter", {
                            content: e.textContent
                        })
                    }
                }, {
                    type: "raw",
                    priority: 4,
                    isMatch: function (e) {
                        return "PRE" === e.nodeName && "EnlighterJSRAW" === e.className
                    },
                    transform: function (e) {
                        return createBlock("zibllblock/enlighter", {
                            content: e.textContent,
                            language: e.dataset.enlighterLanguage || "",
                            theme: e.dataset.enlighterTheme || "",
                            highlight: e.dataset.enlighterHighlight || "",
                            linenumbers: e.dataset.enlighterLinenumbers || "",
                            lineoffset: e.dataset.enlighterLineoffset || "",
                            title: e.dataset.enlighterTitle || "",
                            group: e.dataset.enlighterGroup || ""
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/code", "core/preformatted", "core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/enlighter", {
                            content: t
                        })
                    }
                }],
                to: [{
                    type: "block",
                    blocks: ["core/code"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/code", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/preformatted"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/preformatted", {
                            content: t
                        })
                    }
                }]
            },
            edit: function (props) {
                var content = props.attributes.content,
                    typeClass = props.attributes.typeClass || 'qe_bt_zts',
                    isSelected = props.isSelected;

                var t, n, l = props.attributes,
                    r = props.setAttributes;


                var sm = el(Toolbar, null, el(DropdownMenu, {
                        className: "enlighter-dropdownmenu",
                        icon: "editor-paste-text",
                        label: "设置代码语言",
                        controls: [{
                            title: 'yaml',
                            value: 'yaml',
                            onClick: function () {
                                return r({
                                    c_language: 'yaml'
                                })
                            }
                        }, {
                            title: 'xml/html',
                            value: 'xml',
                            onClick: function () {
                                return r({
                                    c_language: 'xml'
                                })
                            }
                        }, {
                            title: 'visualbasic',
                            value: 'visualbasic',
                            onClick: function () {
                                return r({
                                    c_language: 'visualbasic'
                                })
                            }
                        }, {
                            title: 'vhdl',
                            value: 'vhdl',
                            onClick: function () {
                                return r({
                                    c_language: 'vhdl'
                                })
                            }
                        }, {
                            title: 'typescript',
                            value: 'typescript',
                            onClick: function () {
                                return r({
                                    c_language: 'typescript'
                                })
                            }
                        }, {
                            title: 'swift',
                            value: 'swift',
                            onClick: function () {
                                return r({
                                    c_language: 'swift'
                                })
                            }
                        }, {
                            title: 'squirrel',
                            value: 'squirrel',
                            onClick: function () {
                                return r({
                                    c_language: 'squirrel'
                                })
                            }
                        }, {
                            title: 'sql',
                            value: 'sql',
                            onClick: function () {
                                return r({
                                    c_language: 'sql'
                                })
                            }
                        }, {
                            title: 'shell',
                            value: 'shell',
                            onClick: function () {
                                return r({
                                    c_language: 'shell'
                                })
                            }
                        }, {
                            title: 'scss/sass',
                            value: 'scss',
                            onClick: function () {
                                return r({
                                    c_language: 'scss'
                                })
                            }
                        }, {
                            title: 'rust',
                            value: 'rust',
                            onClick: function () {
                                return r({
                                    c_language: 'rust'
                                })
                            }
                        }, {
                            title: 'ruby',
                            value: 'ruby',
                            onClick: function () {
                                return r({
                                    c_language: 'ruby'
                                })
                            }
                        }, {
                            title: 'raw',
                            value: 'raw',
                            onClick: function () {
                                return r({
                                    c_language: 'raw'
                                })
                            }
                        }, {
                            title: 'python',
                            value: 'python',
                            onClick: function () {
                                return r({
                                    c_language: 'python'
                                })
                            }
                        }, {
                            title: 'prolog',
                            value: 'prolog',
                            onClick: function () {
                                return r({
                                    c_language: 'prolog'
                                })
                            }
                        }, {
                            title: 'powershell',
                            value: 'powershell',
                            onClick: function () {
                                return r({
                                    c_language: 'powershell'
                                })
                            }
                        }, {
                            title: 'php',
                            value: 'php',
                            onClick: function () {
                                return r({
                                    c_language: 'php'
                                })
                            }
                        }, {
                            title: 'nsis',
                            value: 'nsis',
                            onClick: function () {
                                return r({
                                    c_language: 'nsis'
                                })
                            }
                        }, {
                            title: 'matlab',
                            value: 'matlab',
                            onClick: function () {
                                return r({
                                    c_language: 'matlab'
                                })
                            }
                        }, {
                            title: 'markdown',
                            value: 'markdown',
                            onClick: function () {
                                return r({
                                    c_language: 'markdown'
                                })
                            }
                        }, {
                            title: 'lua',
                            value: 'lua',
                            onClick: function () {
                                return r({
                                    c_language: 'lua'
                                })
                            }
                        }, {
                            title: 'less',
                            value: 'less',
                            onClick: function () {
                                return r({
                                    c_language: 'less'
                                })
                            }
                        }, {
                            title: 'kotlin',
                            value: 'kotlin',
                            onClick: function () {
                                return r({
                                    c_language: 'kotlin'
                                })
                            }
                        }, {
                            title: 'json',
                            value: 'json',
                            onClick: function () {
                                return r({
                                    c_language: 'json'
                                })
                            }
                        }, {
                            title: 'javascript',
                            value: 'javascript',
                            onClick: function () {
                                return r({
                                    c_language: 'javascript'
                                })
                            }
                        }, {
                            title: 'java',
                            value: 'java',
                            onClick: function () {
                                return r({
                                    c_language: 'java'
                                })
                            }
                        }, {
                            title: 'ini/conf',
                            value: 'ini',
                            onClick: function () {
                                return r({
                                    c_language: 'ini'
                                })
                            }
                        }, {
                            title: 'groovy',
                            value: 'groovy',
                            onClick: function () {
                                return r({
                                    c_language: 'groovy'
                                })
                            }
                        }, {
                            title: 'go/golang',
                            value: 'go',
                            onClick: function () {
                                return r({
                                    c_language: 'go'
                                })
                            }
                        }, {
                            title: 'docker',
                            value: 'dockerfile',
                            onClick: function () {
                                return r({
                                    c_language: 'dockerfile'
                                })
                            }
                        }, {
                            title: 'diff',
                            value: 'diff',
                            onClick: function () {
                                return r({
                                    c_language: 'diff'
                                })
                            }
                        }, {
                            title: 'cordpro',
                            value: 'cordpro',
                            onClick: function () {
                                return r({
                                    c_language: 'cordpro'
                                })
                            }
                        }, {
                            title: 'cython',
                            value: 'cython',
                            onClick: function () {
                                return r({
                                    c_language: 'cython'
                                })
                            }
                        }, {
                            title: 'css',
                            value: 'css',
                            onClick: function () {
                                return r({
                                    c_language: 'css'
                                })
                            }
                        }, {
                            title: 'csharp',
                            value: 'csharp',
                            onClick: function () {
                                return r({
                                    c_language: 'csharp'
                                })
                            }
                        }, {
                            title: 'Cpp/C++/C',
                            value: 'cpp',
                            onClick: function () {
                                return r({
                                    c_language: 'cpp'
                                })
                            }
                        }, {
                            title: 'avrassembly',
                            value: 'avrassembly',
                            onClick: function () {
                                return r({
                                    c_language: 'avrassembly'
                                })
                            }
                        }, {
                            title: 'assembly',
                            value: 'assembly',
                            onClick: function () {
                                return r({
                                    c_language: 'assembly'
                                })
                            }
                        }, {
                            title: '自动识别',
                            value: 'generic',
                            onClick: function () {
                                return r({
                                    c_language: 'generic'
                                })
                            }
                        }]
                    })),
                    sp = el(PlainText, {
                        value: l.content,
                        onChange: function (e) {
                            return r({
                                content: e
                            })
                        },
                        placeholder: "请输入代码...",
                        "aria-label": "Code"
                    })
                ss = el(SelectControl, {
                        label: "代码语言",
                        value: l.c_language,
                        options: [{
                            label: 'yaml',
                            value: 'yaml'
                        }, {
                            label: 'xml/html',
                            value: 'xml'
                        }, {
                            label: 'visualbasic',
                            value: 'visualbasic'
                        }, {
                            label: 'vhdl',
                            value: 'vhdl'
                        }, {
                            label: 'typescript',
                            value: 'typescript'
                        }, {
                            label: 'swift',
                            value: 'swift'
                        }, {
                            label: 'squirrel',
                            value: 'squirrel'
                        }, {
                            label: 'sql',
                            value: 'sql'
                        }, {
                            label: 'shell',
                            value: 'shell'
                        }, {
                            label: 'scss/sass',
                            value: 'scss'
                        }, {
                            label: 'rust',
                            value: 'rust'
                        }, {
                            label: 'ruby',
                            value: 'ruby'
                        }, {
                            label: 'raw',
                            value: 'raw'
                        }, {
                            label: 'python',
                            value: 'python'
                        }, {
                            label: 'prolog',
                            value: 'prolog'
                        }, {
                            label: 'powershell',
                            value: 'powershell'
                        }, {
                            label: 'php',
                            value: 'php'
                        }, {
                            label: 'nsis',
                            value: 'nsis'
                        }, {
                            label: 'matlab',
                            value: 'matlab'
                        }, {
                            label: 'markdown',
                            value: 'markdown'
                        }, {
                            label: 'lua',
                            value: 'lua'
                        }, {
                            label: 'less',
                            value: 'less'
                        }, {
                            label: 'kotlin',
                            value: 'kotlin'
                        }, {
                            label: 'json',
                            value: 'json'
                        }, {
                            label: 'javascript',
                            value: 'javascript'
                        }, {
                            label: 'java',
                            value: 'java'
                        }, {
                            label: 'ini/conf',
                            value: 'ini'
                        }, {
                            label: 'groovy',
                            value: 'groovy'
                        }, {
                            label: 'go/golang',
                            value: 'go'
                        }, {
                            label: 'docker',
                            value: 'dockerfile'
                        }, {
                            label: 'diff',
                            value: 'diff'
                        }, {
                            label: 'cordpro',
                            value: 'cordpro'
                        }, {
                            label: 'cython',
                            value: 'cython'
                        }, {
                            label: 'css',
                            value: 'css'
                        }, {
                            label: 'csharp',
                            value: 'csharp'
                        }, {
                            label: 'Cpp/C++/C',
                            value: 'cpp'
                        }, {
                            label: 'avrassembly',
                            value: 'avrassembly'
                        }, {
                            label: 'assembly',
                            value: 'assembly'
                        }, {
                            label: '自动识别',
                            value: 'generic'
                        }],
                        onChange: function (e) {
                            return r({
                                c_language: e
                            })
                        }
                    }),
                    sz = el(InspectorControls, null,
                        el(PanelBody, {
                                icon: "admin-appearance",
                                title: "代码设置"
                            }, ss,
                            el(SelectControl, {
                                label: "选择主题",
                                value: l.theme,
                                options: [{
                                    label: 'enlighter',
                                    value: 'enlighter'
                                }, {
                                    label: 'classic',
                                    value: 'classic'
                                }, {
                                    label: 'beyond',
                                    value: 'beyond'
                                }, {
                                    label: 'mowtwo',
                                    value: 'mowtwo'
                                }, {
                                    label: 'eclipse',
                                    value: 'eclipse'
                                }, {
                                    label: 'droide',
                                    value: 'droide'
                                }, {
                                    label: 'minimal',
                                    value: 'minimal'
                                }, {
                                    label: 'atomic',
                                    value: 'atomic'
                                }, {
                                    label: 'dracula',
                                    value: 'dracula'
                                }, {
                                    label: 'bootstrap4',
                                    value: 'bootstrap4'
                                }, {
                                    label: 'rowhammer',
                                    value: 'rowhammer'
                                }, {
                                    label: 'godzilla',
                                    value: 'godzilla'
                                }, {
                                    label: '跟随主题设置',
                                    value: ''
                                }],
                                onChange: function (e) {
                                    return r({
                                        theme: e
                                    })
                                }
                            }), el(RadioControl, {
                                label: "显示行号",
                                selected: l.linenumbers,
                                options: [{
                                    label: "跟随主题设置",
                                    value: ""
                                }, {
                                    label: "显示",
                                    value: "true"
                                }, {
                                    label: "隐藏",
                                    value: "false"
                                }],
                                onChange: function (e) {
                                    return r({
                                        linenumbers: e
                                    })
                                }
                            }), el(TextControl, {
                                label: "起始行号",
                                value: l.lineoffset,
                                onChange: function (e) {
                                    return r({
                                        lineoffset: e
                                    })
                                },
                                placeholder: "输入行号。例：12"
                            }), el(TextControl, {
                                label: "高亮行号",
                                value: l.highlight,
                                onChange: function (e) {
                                    return r({
                                        highlight: e
                                    })
                                },
                                placeholder: "格式：1,2,20-22"
                            })), el(PanelBody, {
                                title: "代码组",
                                initialOpen: !1,
                                icon: "excerpt-view"
                            },
                            el("p", null, "如果需要加入代码组，请填写下面设置，相同组ID的代码将合并为代码组显示"),
                            el(TextControl, {
                                label: "代码标题",
                                value: l.title,
                                onChange: function (e) {
                                    return r({
                                        title: e
                                    })
                                },
                                placeholder: "加入组之后显示的标题"
                            }), el(TextControl, {
                                label: "自定义组id",
                                value: l.group,
                                onChange: function (e) {
                                    return r({
                                        group: e
                                    })
                                },
                                placeholder: "自定义组的id"
                            })));

                return el("div", null, el(Fragment, null, el(BlockControls, null, sm)),
                    el("div", {
                            className: "enlighter-block-wrapper"
                        },
                        el("div", {
                                className: "enlighter-header"
                            },
                            el("div", {
                                className: "enlighter-title"
                            })), el('pre', {
                                tagName: 'pre',
                                className: "enlighter-pre",
                            },
                            el("div", {
                                className: "enlighter-label"
                            }, "语言：", l.c_language, " · 主题：", l.theme ? l.theme : "跟随主题"), sp
                        ),
                        el("div", {
                            className: "enlighter-footer"
                        }), sz
                    ))

            },
            save: function (e) {
                var t = e.attributes,
                    tt = el("code", {
                            className: "gl",
                            "data-enlighter-language": t.c_language,
                            "data-enlighter-theme": t.theme,
                            "data-enlighter-highlight": t.highlight,
                            "data-enlighter-linenumbers": t.linenumbers,
                            "data-enlighter-lineoffset": t.lineoffset,
                            "data-enlighter-title": t.title,
                            "data-enlighter-group": t.group
                        },
                        t.content);
                return el("pre", {}, tt)
            }
        });
        //-------------------------------------------
        rB('zibllblock/biaoti', {
            title: 'Zibll:标题',
            icon: icon.biaoti,
            category: 'zibll_block_cat',
            description: "和主题样式匹配的文章标题，可自定义颜色",
            className: false,
            attributes: {
                content: {
                    type: 'array',
                    source: 'children',
                    selector: 'h1',
                },
                typeClass: {
                    source: 'attribute',
                    selector: '.title-theme',
                    attribute: 'class',
                },
                color: {
                    source: 'attribute',
                    selector: 'h1',
                    attribute: 'data-color',
                }
            },
            transforms: {
                from: [{
                    type: "block",
                    blocks: ["core/heading", "core/preformatted", "core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/biaoti", {
                            content: t
                        })
                    }
                }, ],
                to: [{
                    type: "block",
                    blocks: ["core/heading"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/heading", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/paragraph", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/preformatted"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/preformatted", {
                            content: t
                        })
                    }
                }]
            },
            edit: function (props) {
                var content = props.attributes.content,
                    typeClass = content.typeClass || 'title-theme',
                    isSelected = props.isSelected;
                color = props.attributes.color;
                sty = color && '--theme-color:' + color;

                function onChangeContent(newContent) {
                    props.setAttributes({
                        content: newContent
                    });
                }

                function changeType(event) {
                    var type = event.target.className;
                    props.setAttributes({
                        typeClass: 'title-theme ' + type
                    });
                }

                function changecolor(c) {
                    props.setAttributes({
                        color: c
                    });
                }

                var richText = el(
                    RichText, {
                        tagName: 'div',
                        onChange: onChangeContent,
                        value: content,
                        isSelected: props.isSelected,
                        placeholder: '请输入标题...'
                    });

                var outerHtml = el('div', {
                    className: typeClass,
                    'data-color': color,
                    style: {
                        color: color,
                        'border-left-color': color
                    }
                }, el('h1', {}, richText));
                var selector = el('div', {
                    className: 'g_extend anz'
                }, [
                    el('button', {
                        className: 'qe_bt_zts',
                        onClick: changeType
                    }, '主题色'),
                    el('button', {
                        className: 'qe_bt_lan',
                        onClick: changeType
                    }, '蓝色'),
                    el('button', {
                        className: 'qe_bt_hui',
                        onClick: changeType
                    }, '灰色'),
                    el('button', {
                        className: 'qe_bt_c-red',
                        onClick: changeType
                    }, '红色'),
                ]);
                var Co = el(c.ColorPalette, {
                    value: color,
                    colors: colors,
                    className: "q-ColorPalette",
                    onChange: changecolor
                });

                return el('div', {}, outerHtml,
                    el(InspectorControls, null,
                        el(PanelBody, {
                                title: "自定义颜色"
                            },
                            el('p', {
                                className: "components-base-control",
                            }, '默认颜色为主题高亮颜色，如需要自定义颜色，请在下方选择颜色'), el('p', {
                                className: "components-base-control",
                            }, Co))));

            },

            save: function (props) {
                var content = props.attributes.content,
                    typeClass = props.attributes.typeClass || 'title-theme',
                    color = props.attributes.color;
                sty = color && '--theme-color:' + color;

                var outerHtml = el('h1', {
                    'data-color': color,
                    className: typeClass,
                    style: sty
                }, content);

                return outerHtml;
            }
        });
        //---------------------------------------------
        rB('zibllblock/hide-content', {
            title: 'Zibll:隐藏内容',
            icon: {
                src: 'hidden',
                foreground: icon_color
            },
            description: '隐藏文章部分内容，多种隐藏可见模式(评论可见、付费阅读、登录可见、密码验证、会员可见)',
            category: 'zibll_block_cat',
            attributes: {
                type: {
                    source: 'attribute',
                    selector: 'div',
                    attribute: 'data-type',
                },
                password: {
                    source: 'attribute',
                    selector: '[data-password]',
                    attribute: 'data-password',
                    default: ''
                },
                img_id: {
                    source: 'attribute',
                    selector: '[data-img-id]',
                    attribute: 'data-img-id',
                    default: ''
                },
                img_url: {
                    source: 'attribute',
                    selector: '[data-img-url]',
                    attribute: 'data-img-url',
                    default: ''
                },
                desc: {
                    source: 'attribute',
                    selector: '[data-desc]',
                    attribute: 'data-desc',
                    default: ''
                },
            },
            edit: function (props) {
                var isSelected = props.isSelected,
                    sa = props.setAttributes,
                    pa = props.attributes,
                    type_v = pa.type || 'reply',
                    xzk = el('div', {
                        className: 'hide-innerblocks'
                    }, el(InnerBlocks)),
                    text = {
                        'reply': '评论可见',
                        'payshow': '付费阅读',
                        'logged': '登录可见',
                        'password': '密码验证',
                        'vip1': '一级会员可见',
                        'vip2': '二级会员可见'
                    };
                var gjl = el(Toolbar, {}, el(DropdownMenu, {
                        icon: "editor-paste-text",
                        className: 'zibllblock-buttons-sl',
                        label: "隐藏模式选择",
                        controls: [{
                            title: text.reply,
                            value: 'reply',
                            onClick: function (e) {
                                sa({
                                    type: 'reply'
                                })
                            }
                        }, {
                            title: text.logged,
                            value: 'logged',
                            onClick: function (e) {
                                sa({
                                    type: 'logged'
                                })
                            }
                        }, {
                            title: text.password,
                            value: 'password',
                            onClick: function (e) {
                                sa({
                                    type: 'password'
                                })
                            }
                        }, {
                            title: text.payshow,
                            value: 'payshow',
                            onClick: function (e) {
                                sa({
                                    type: 'payshow'
                                })
                            }
                        }, {
                            title: text.vip1,
                            value: 'vip1',
                            onClick: function (e) {
                                sa({
                                    type: 'vip1'
                                })
                            }
                        }, {
                            title: text.vip2,
                            value: 'vip2',
                            onClick: function (e) {
                                sa({
                                    type: 'vip2'
                                })
                            }
                        }]
                    })),
                    dqk = el(Fragment, null, el(BlockControls, null, gjl));

                return el('div', {
                        className: 'hide-content'
                    }, dqk, el('div', {
                            className: 'hide-title'
                        }, '【 隐藏内容 】- ' + '【 ' + text[type_v] + ' 】',
                        el('span', {
                            className: 'dashicons dashicons-admin-generic',
                            style: {
                                'float': 'right',
                                'margin': '5px 10px',
                            }
                        })
                    ), xzk,
                    el(InspectorControls, null,
                        help_link('https://www.zibll.com/853.html'),
                        el(PanelBody, {
                                title: "隐藏内容设置"
                            },
                            el(SelectControl, {
                                label: "隐藏可见模式",
                                value: type_v,
                                options: [{
                                    label: text.reply,
                                    value: 'reply'
                                }, {
                                    label: text.logged,
                                    value: 'logged'
                                }, {
                                    label: text.password,
                                    value: 'password'
                                }, {
                                    label: text.payshow,
                                    value: 'payshow'
                                }, {
                                    label: text.vip1,
                                    value: 'vip1'
                                }, {
                                    label: text.vip2,
                                    value: 'vip2'
                                }],
                                onChange: function (e) {
                                    sa({
                                        type: e
                                    })
                                }
                            }), [type_v == 'payshow' && el('div', {
                                className: 'block-editor-block-card__description'
                            }, '付费阅读：请配合底部 付费功能-付费阅读 功能使用')],
                            [type_v == 'password' && el('div', {
                                    className: 'block-editor-block-card__description'
                                },
                                el(TextControl, {
                                    label: "设置密码",
                                    value: pa.password || '',
                                    onChange: function (e) {
                                        return sa({
                                            password: e
                                        })
                                    },
                                    placeholder: "请输入密码..."
                                }),
                                el(TextControl, {
                                    label: "提醒文案",
                                    value: pa.desc || '',
                                    onChange: function (e) {
                                        return sa({
                                            desc: e
                                        })
                                    },
                                    placeholder: "请输入提醒内容...",
                                }),
                                [pa.img_id && el('div', {
                                    className: " ",
                                }, el('img', {
                                    src: pa.img_url,
                                }, ))],
                                el(MediaUpload, {
                                    title: "提醒图片",
                                    allowedTypes: ["image"],
                                    onSelect: function onSelect(media) {
                                        sa({
                                            img_url: media.url,
                                            img_id: media.id
                                        });
                                    },
                                    value: pa.img_id,
                                    render: function render(_ref) {
                                        var open = _ref.open;
                                        return el(Button, {
                                            className: "but jb-blue is-secondary",
                                            onClick: open
                                        }, [pa.img_id ? '替换提醒图片' : '添加图片提醒']);
                                    }
                                }),
                                el('div', {
                                    className: "mt10 em09",
                                }, '通过提醒文案和提醒图片设置，可实现引导用户关注微信公众号获取密码引流等功能。'),
                                el('div', {
                                    className: "mt6 em09",
                                }, '注意：相同密码的块一篇文章只能添加一个，如需同一篇文章添加多个此模块，请设置不同密码'),
                            )],
                        )));
            },
            save: function (props) {
                var pa = props.attributes;
                var type = pa.type || 'reply';
                var tag = {};
                var tag_2 = '';
                if (type == 'password') {
                    tag = {
                        'data-password': pa.password,
                        'data-img-id': pa.img_id,
                        'data-img-url': pa.img_url,
                        'data-desc': pa.desc,
                    }
                    tag_2 = ' password="' + pa.password + '" img_id="' + pa.img_id + '" img_url="' + pa.img_url + '" desc="' + pa.desc + '"'
                }
                return el('div', {
                    'data-type': type
                }, [el('span', {}, '[hidecontent type="' + type + '"' + tag_2 + ']'), el(InnerBlocks.Content), el('span', tag, '[/hidecontent]')]);
            },
        });


        rB('zibllblock/postsbox', {
            title: 'Zibll:文章',
            icon: icon.postsbox,
            attributes: {
                post_id: {
                    source: 'attribute',
                    selector: 'div',
                    attribute: 'data-pid',
                }
            },
            description: '显示一篇文章',
            category: 'zibll_block_cat',
            edit: function (props) {
                var isSelected = props.isSelected,
                    content = props.attributes.post_id;

                function onChangeContent(e) {
                    props.setAttributes({
                        post_id: e
                    });
                }
                var rti = el(
                    TextControl, {
                        tagName: 'div',
                        onChange: onChangeContent,
                        value: content,
                        type: 'number',
                        placeholder: '请输入文章ID',
                        label: '请输入文章ID'
                    });
                return el('div', {
                        className: 'postsbox'
                    }, el('div', {
                        className: 'postsbox-doc'
                    }, '显示一篇文章模块'),
                    rti, el(InspectorControls, null,
                        help_link('https://www.zibll.com/860.html')
                    ));
            },
            save: function (props) {
                var post_id = props.attributes.post_id;
                return el('div', {
                    'data-pid': post_id
                }, '[postsbox post_id="' + post_id + '"]');

            },
        });

        rB('zibllblock/quote', {
            title: 'Zibll:引言',
            icon: {
                src: 'format-quote',
                foreground: icon_color
            },
            description: '几种不同的引言框',
            category: 'zibll_block_cat',
            attributes: {
                content: {
                    type: 'array',
                    source: 'children',
                    selector: '.quote_q p',
                },
                typeClass: {
                    source: 'attribute',
                    selector: '.quote_q',
                    attribute: 'class',
                },
                color: {
                    source: 'attribute',
                    selector: '.quote_q',
                    attribute: 'data-color',
                }
            },
            transforms: {
                from: [{
                    type: "block",
                    blocks: ["zibllblock/alert", "core/quote", "core/preformatted", "core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/quote", {
                            content: t
                        })
                    }
                }, ],
                to: [{
                    type: "block",
                    blocks: ["core/quote"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/quote", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["zibllblock/alert"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/alert", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/paragraph", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/preformatted"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/preformatted", {
                            content: t
                        })
                    }
                }]
            },
            edit: function (props) {
                var content = props.attributes.content,
                    typeClass = props.attributes.typeClass || 'quote_q',
                    isSelected = props.isSelected;
                color = props.attributes.color;
                sty = color ? color : '';

                function changecolor(e) {
                    props.setAttributes({
                        color: e
                    });
                }

                function onChangeContent(e) {
                    props.setAttributes({
                        content: e
                    });
                }

                function changeType(e) {
                    var type = e.target.className;
                    props.setAttributes({
                        typeClass: 'quote_q ' + type
                    });
                }

                var richText = el(
                    RichText, {
                        tagName: 'div',
                        isSelected: props.isSelected,
                        onChange: onChangeContent,
                        value: content,
                        placeholder: '请输入内容...'
                    });
                var outerHtml = el('div', {
                    className: typeClass,
                    style: {
                        '--quote-color': sty
                    }
                }, el('i', {
                    className: "fa fa-quote-left"
                }), richText);


                var Co = el(c.ColorPalette, {
                    value: color || '#555',
                    colors: colors,
                    className: "q-ColorPalette",
                    onChange: changecolor
                });

                return el('div', {}, outerHtml, el('div', {},
                    el(InspectorControls, null,
                        el(PanelBody, {
                                title: "自定义颜色"
                            },
                            el('p', {}, '默认为主题颜色，如果需自定义请在下方选择颜色（引言默认透明度为70%，请不要选择较浅的颜色，并请注意深色主题的显示效果）'),
                            el('p', {}, Co)))));
            },
            save: function (props) {
                var content = props.attributes.content,
                    typeClass = props.attributes.typeClass || 'quote_q';
                color = props.attributes.color;
                sty = color && '--quote-color:' + color;

                var outerHtml = el('div', {
                    className: typeClass,
                    'data-color': color,
                    style: sty
                }, el('i', {
                    className: 'fa fa-quote-left'
                }), el('p', {}, content));

                return el('div', {}, outerHtml);

            },
        });
        //-------------------------------------------------------------
        rB('zibllblock/alert', {
            title: 'Zibll:提醒框',
            icon: {
                src: 'info',
                foreground: icon_color
            },
            description: '几种不同的提醒框，可选择关闭按钮',
            category: 'zibll_block_cat',
            attributes: {
                content: {
                    type: 'array',
                    source: 'children',
                    selector: 'div.alert',
                },
                typeClass: {
                    source: 'attribute',
                    selector: '.alert',
                    attribute: 'class',
                },
                isChecked: {
                    source: 'attribute',
                    selector: '.alert',
                    attribute: "data-isclose"
                }
            },
            transforms: {
                from: [{
                    type: "block",
                    blocks: ["zibllblock/quote", "core/quote", "core/preformatted", "core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/alert", {
                            content: t
                        })
                    }
                }, ],
                to: [{
                    type: "block",
                    blocks: ["core/quote"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/quote", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["zibllblock/quote"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/quote", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/paragraph", {
                            content: t
                        })
                    }
                }, {
                    type: "block",
                    blocks: ["core/preformatted"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("core/preformatted", {
                            content: t
                        })
                    }
                }]
            },
            edit: function (props) {
                var content = props.attributes.content,
                    typeClass = props.attributes.typeClass || 'alert jb-blue',
                    isChecked = props.attributes.isChecked,
                    isSelected = props.isSelected;

                function onChangeContent(e) {
                    props.setAttributes({
                        content: e
                    });
                }

                function onisChecked(e) {
                    props.setAttributes({
                        isChecked: e
                    });
                }

                function changeType(e) {
                    var type = e.target.className;
                    props.setAttributes({
                        typeClass: 'alert ' + type
                    });
                }
                var richText = el(
                    RichText, {
                        tagName: 'div',
                        isSelected: props.isSelected,
                        onChange: onChangeContent,
                        value: content,
                        placeholder: '请输入内容...'
                    });

                var outerHtml = el('div', {
                    className: typeClass
                }, richText);

                var selector = el('span', {
                        className: 'g_extend anz alert_b'
                    }, [
                        el('button', {
                            className: 'jb-blue',
                            onClick: changeType
                        }),
                        el('button', {
                            className: 'jb-green',
                            onClick: changeType
                        }),
                        el('button', {
                            className: 'jb-yellow',
                            onClick: changeType
                        }),
                        el('button', {
                            className: 'jb-red',
                            onClick: changeType
                        }),
                    ]),
                    closebutton = el('div', {
                        className: 'close_an',
                    }, el(ToggleControl, {
                        label: '提醒框可关闭',
                        checked: isChecked,
                        onChange: onisChecked
                    }));

                return el('div', {}, [outerHtml, isChecked && el('button', {
                        className: 'close'
                    }, '×'), isSelected && selector, isSelected && closebutton],
                    el(InspectorControls, null,
                        el(PanelBody, {
                            icon: "admin-appearance",
                            title: "提醒框设置"
                        }, el('div', {}, '提醒框类型'), el('div', {}, selector)), closebutton))

            },
            save: function (props) {
                var content = props.attributes.content,
                    isChecked = props.attributes.isChecked || '',
                    typeClass = props.attributes.typeClass || 'alert jb-blue';
                var Close = el('button', {
                    'type': 'button',
                    className: 'close',
                    'data-dismiss': 'alert',
                    'aria-label': 'Close'
                }, el('div', {
                    'data-class': "ic-close",
                    'data-svg': "close",
                    'data-viewbox': "0 0 1024 1024"
                }));

                var outerHtml = el('div', {
                    className: typeClass,
                    "data-isclose": isChecked,
                    "role": 'alert'
                }, content);
                return el('div', {
                    className: 'alert-dismissible fade in'
                }, [isChecked && Close], outerHtml);
            },
        });
        //-------------------------------------------------------------
        rB('zibllblock/buttons', {
            title: 'Zibll:按钮组',
            description: '多种样式的按钮',
            icon: icon.buttons,
            category: 'zibll_block_cat',
            attributes: {
                alignment: {
                    type: 'string',
                },
                quantity: {
                    type: "attribute",
                    attribute: "data-quantity",
                    default: 1
                },
                radius: {
                    type: "attribute",
                    attribute: "data-radius",
                    default: true
                },
                content1: {
                    type: 'array',
                    source: 'children',
                    selector: 'span.an_1',
                },
                typeClass1: {
                    source: 'attribute',
                    selector: '.an_1',
                    attribute: 'class',
                },
                content2: {
                    type: 'array',
                    source: 'children',
                    selector: 'span.an_2',
                },
                typeClass2: {
                    source: 'attribute',
                    selector: '.an_2',
                    attribute: 'class',
                },
                content3: {
                    type: 'array',
                    source: 'children',
                    selector: 'span.an_3',
                },
                typeClass3: {
                    source: 'attribute',
                    selector: '.an_3',
                    attribute: 'class',
                },
                content4: {
                    type: 'array',
                    source: 'children',
                    selector: 'span.an_4',
                },
                typeClass4: {
                    source: 'attribute',
                    selector: '.an_4',
                    attribute: 'class',
                },
                content5: {
                    type: 'array',
                    source: 'children',
                    selector: 'span.an_5',
                },
                typeClass5: {
                    source: 'attribute',
                    selector: '.an_5',
                    attribute: 'class',
                }
            },
            transforms: {
                from: [{
                    type: "block",
                    blocks: ["core/paragraph"],
                    transform: function (e) {
                        var t = e.content;
                        return createBlock("zibllblock/buttons", {
                            content1: t
                        })
                    }
                }, ],
                to: [{
                    type: "block",
                    blocks: ["core/paragraph"],
                    transform: function (e) {
                        var t = e.content1;
                        return createBlock("core/paragraph", {
                            content: t
                        })
                    }
                }]
            },
            edit: function (props) {
                var at = props.attributes,
                    sa = props.setAttributes,
                    alignment = at.alignment,
                    isS = props.isSelected,
                    sl = at.quantity,
                    rd = at.radius,
                    c = [];

                for (let i = 0; i <= 5; i++) {
                    c['c' + i] = at['content' + i],
                        c['cs' + i] = at['typeClass' + i] || 'but b-blue',
                        c['rt' + i] = el(
                            RichText, {
                                tagName: 'div',
                                onChange: function (e) {
                                    sa({
                                        ['content' + i]: e
                                    })
                                },
                                value: c['c' + i],
                                isSelected: props.isS,
                                placeholder: '按钮-' + i
                            }),
                        c['crt' + i] = el('div', {
                            className: c['cs' + i],
                        }, c['rt' + i]),
                        c['bk' + i] = el('button', {
                            className: 'anz sz',
                            onClick: function (e) {
                                $('.anz.an.x' + i).slideToggle(200)
                            }
                        }, el('span', {
                            className: 'dashicons dashicons-admin-appearance'
                        })),
                        c['btt' + i] = el('div', {
                                className: 'g_extend anz an x' + i
                            },
                            el('button', {
                                className: 'but b-red',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but b-yellow',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but b-blue',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but b-green',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but b-purple',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but hollow c-red',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but hollow c-yellow',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but hollow c-blue',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but hollow c-green',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but hollow c-purple',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but jb-red',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but jb-yellow',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but jb-blue',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but jb-green',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                            el('button', {
                                className: 'but jb-purple',
                                onClick: function (e) {
                                    sa({
                                        ['typeClass' + i]: 'an_' + i + ' ' + e.target.className
                                    })
                                }
                            }, ''),
                        );
                }

                var gjl = el(Toolbar, {}, el(DropdownMenu, {
                        icon: "plus-alt",
                        className: 'zibllblock-buttons-sl',
                        label: "按钮数量",
                        controls: [{
                                title: '1个',
                                value: 1,
                                onClick: function (e) {
                                    sa({
                                        quantity: 1
                                    })
                                }
                            }, {
                                title: '2个',
                                onClick: function (e) {
                                    sa({
                                        quantity: 2
                                    })
                                }
                            }, {
                                title: '3个',
                                value: 3,
                                onClick: function (e) {
                                    sa({
                                        quantity: 3
                                    })
                                }
                            }, {
                                title: '4个',
                                value: 4,
                                onClick: function (e) {
                                    sa({
                                        quantity: 4
                                    })
                                }
                            }, {
                                title: '5个',
                                value: 5,
                                onClick: function (e) {
                                    sa({
                                        quantity: 5
                                    })
                                }
                            }

                        ]
                    })),
                    dqk = el(Fragment, null, el(BlockControls, null, gjl, el(AlignmentToolbar, {
                        value: alignment,
                        onChange: function (e) {
                            sa({
                                alignment: e
                            })
                        }
                    })));

                return el('div', {
                        style: {
                            textAlign: alignment
                        },
                        className: 'aniuzu ' + (rd && 'radius')
                    }, dqk,
                    [c.crt1, isS && c.bk1, isS && c.btt1],
                    [sl >= 2 && [c.crt2, isS && c.bk2, isS && c.btt2]],
                    [sl >= 3 && [c.crt3, isS && c.bk3, isS && c.btt3]],
                    [sl >= 4 && [c.crt4, isS && c.bk4, isS && c.btt4]],
                    [sl >= 5 && [c.crt5, isS && c.bk5, isS && c.btt5]],
                    el(InspectorControls, null,
                        el(PanelBody, {
                            icon: "admin-generic",
                            title: "按钮设置"
                        }, el(RangeControl, {
                            label: "按钮数量",
                            value: sl,
                            onChange: function (e) {
                                sa({
                                    quantity: e
                                })
                            },
                            min: "1",
                            max: "5"
                        }), el(ToggleControl, {
                            className: 'close_an',
                            label: '按钮圆角',
                            checked: rd,
                            onChange: function (e) {
                                sa({
                                    radius: e
                                })
                            }
                        }))
                    ));
            },
            save: function (props) {
                var at = props.attributes,
                    sa = props.setAttributes,
                    alignment = at.alignment,
                    isSelected = props.isSelected,
                    sl = at.quantity,
                    rd = at.radius,
                    c = [];

                for (let i = 0; i <= 5; i++) {
                    c[i] = at['content' + i],
                        c['s' + i] = at['typeClass' + i] || 'an_' + i + ' but b-blue',
                        c['jg' + i] = el('span', {
                            className: c['s' + i]
                        }, c[i]);
                }
                return outerHtml = el('div', {
                        "data-quantity": sl,
                        "data-radius": rd,
                        style: {
                            textAlign: alignment
                        },
                        className: rd && 'radius'
                    },
                    [sl > 0 && c.jg1], [sl > 1 && c.jg2], [sl > 2 && c.jg3], [sl > 3 && c.jg4], [sl > 4 && c.jg5]
                );
            },
        });
        //-------------------------------------------------------------
        rB('zibllblock/carousel', {
            title: 'Zibll:幻灯片',
            description: '选择图片生成幻灯片',
            icon: {
                src: 'images-alt2',
                foreground: icon_color
            },
            category: 'zibll_block_cat',
            attributes: {
                center: {
                    type: 'string',
                    selector: 'div',
                    source: 'attribute',
                    attribute: 'data-cen',
                    default: true
                },
                interval: {
                    type: 'string',
                    selector: '.carousel',
                    source: 'attribute',
                    attribute: 'data-interval',
                    default: 4000
                },
                limitedwidth: {
                    type: 'string',
                    selector: 'div',
                    source: 'attribute',
                    attribute: 'data-liw',
                    default: true
                },
                maxwidth: {
                    type: 'string',
                    source: 'attribute',
                    selector: 'div',
                    attribute: 'data-mw',
                    default: 600
                },
                effect: {
                    type: 'string',
                    selector: '.carousel',
                    source: 'attribute',
                    attribute: 'data-effect',
                    default: ''
                },
                jyloop: {
                    type: 'string',
                    selector: '.carousel',
                    source: 'attribute',
                    attribute: 'data-jyloop',
                    default: true
                },
                id: {
                    type: 'string',
                    selector: '.carousel',
                    source: 'attribute',
                    attribute: 'id',
                },
                proportion: {
                    type: 'string',
                    selector: '.carousel',
                    source: 'attribute',
                    attribute: 'proportion',
                    default: '0.6'
                }
            },
            edit: function (props) {
                var at = props.attributes,
                    isS = props.isSelected,
                    liw = at.limitedwidth,
                    int = at.interval,
                    cn = at.center,
                    mw = at.maxwidth,
                    eff = at.effect,
                    lop = at.jyloop,
                    sa = props.setAttributes,
                    c = {},

                    noticeUI = props.noticeUI;

                var sjs = parseInt((Math.random() + 1) * Math.pow(10, 4));

                if (!at.id) {
                    sa({
                        id: sjs
                    })
                }
                const TEMPLATE = [
                    ['core/gallery', {
                        linkTo: 'none',
                        columns: '8'
                    }]
                ];

                var xzk = el(InnerBlocks, {
                        allowedBlocks: ['core/gallery'],
                        templateLock: '',
                        template: TEMPLATE
                    }, ''),
                    inhg = el(RangeControl, {
                        label: "切换时间（秒）",
                        value: int / 1000,
                        onChange: function (e) {
                            sa({
                                interval: e * 1000
                            })
                        },
                        min: "1",
                        max: "20"
                    }),
                    jzxh = el(ToggleControl, {
                        label: '循环播放',
                        checked: lop,
                        onChange: function (e) {
                            sa({
                                jyloop: e
                            })
                        }
                    }),
                    wdxz = el(ToggleControl, {
                        label: '限制最大宽度',
                        checked: liw,
                        onChange: function (e) {
                            sa({
                                limitedwidth: e
                            })
                        }
                    }),
                    jza = el(ToggleControl, {
                        label: '居中显示',
                        checked: cn,
                        onChange: function (e) {
                            sa({
                                center: e
                            })
                        }
                    }),
                    mwhg = el(RangeControl, {
                        label: "最大宽度",
                        value: mw,
                        onChange: function (e) {
                            sa({
                                maxwidth: e
                            })
                        },
                        min: "200",
                        max: "1500"
                    }),
                    eeff = el(SelectControl, {
                        label: "切换动画",
                        value: eff,
                        onChange: function (e) {
                            sa({
                                effect: e
                            })
                        },
                        options: [{
                            label: '滑动',
                            value: ''
                        }, {
                            label: '淡出淡入',
                            value: 'fade'
                        }, {
                            label: '3D方块',
                            value: 'cube'
                        }, {
                            label: '3D滑入',
                            value: 'coverflow'
                        }, {
                            label: '3D翻转',
                            value: 'flip'
                        }],
                    });

                return el('div', {
                        className: 'carousel iss'
                    }, el('div', {
                            className: 'leab'
                        }, 'Zibll:幻灯片',
                        el('span', {
                            className: 'dashicons dashicons-admin-generic'
                        }),
                    ),
                    xzk,
                    el(InspectorControls, null,
                        help_link('https://www.zibll.com/675.html'),
                        el(PanelBody, {
                                title: "幻灯片设置"
                            }, eeff,
                            el(SelectControl, {
                                label: "保持长宽比例",
                                value: at.proportion,
                                options: [{
                                    label: '禁用',
                                    value: ''
                                }, {
                                    label: '横版-3:1',
                                    value: '0.333'
                                }, {
                                    label: '横版-5:2',
                                    value: '0.4'
                                }, {
                                    label: '横版-2:1',
                                    value: '0.5'
                                }, {
                                    label: '横版-5:3',
                                    value: '0.6'
                                }, {
                                    label: '横版-4:3',
                                    value: '0.75'
                                }, {
                                    label: '横版-5:4',
                                    value: '0.75'
                                }, {
                                    label: '横版-8:7',
                                    value: '0.875'
                                }, {
                                    label: '正方形-1:1',
                                    value: '1'
                                }, {
                                    label: '竖版-7:8',
                                    value: '1.142'
                                }, {
                                    label: '竖版-4:5',
                                    value: '1.25'
                                }, {
                                    label: '竖版-3:4',
                                    value: '1.333'
                                }, {
                                    label: '竖版-3:5',
                                    value: '1.666'
                                }, {
                                    label: '竖版-1:2',
                                    value: '2'
                                }, {
                                    label: '竖版-2:5',
                                    value: '2.5'
                                }, {
                                    label: '竖版-1:3',
                                    value: '3'
                                }],
                                onChange: function (e) {
                                    sa({
                                        proportion: e
                                    })
                                }
                            }), inhg, jzxh, wdxz,
                            liw && [mwhg, jza], el("p", null, "如果幻灯片内的图片尺寸不一致，建议开启限制最大宽度，再结合长宽比例能显示更好的效果")
                        )));

            },

            save: function (props) {
                var at = props.attributes,
                    liw = at.limitedwidth ? 'true' : '',
                    cn = at.center ? 'true' : '',
                    mw = at.maxwidth,
                    int = at.interval,
                    eff = at.effect,
                    lop = at.jyloop ? 'true' : '',
                    mar = liw && cn && '10px auto' || '',
                    mww = liw && mw + 'px' || '',
                    id = at.id;

                var dhl = el('div', {
                        className: 'swiper-button-next'
                    }),
                    dhr = el('div', {
                        className: 'swiper-button-prev'
                    }),
                    zsq = el('div', {
                        className: 'swiper-pagination'
                    });

                return el('div', {
                        "data-mw": mw,
                        "data-liw": liw,
                        "data-cen": cn,
                        className: 'wp-block-carousel'
                    },
                    el('div', {
                            className: 'carousel slide',
                            'data-effect': eff,
                            'data-jyloop': lop,
                            'data-interval': int,
                            'id': id,
                            'proportion': at.proportion,
                            style: {
                                'max-width': mww,
                                'margin': mar
                            }
                        },
                        el(InnerBlocks.Content), dhl, dhr, zsq));
            }
        });

        //-------------------------------------------------------------
        b.updateCategory("zibll_block_cat", {
            icon: icon.zibll
        })
        //-------------------------------------------------------------

    })
})(jQuery);