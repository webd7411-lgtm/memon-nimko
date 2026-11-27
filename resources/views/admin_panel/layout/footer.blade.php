    {{--  @yield('scripts')  --}}

    <footer>
        <div class="footer-area">
            @php
                $footerText = sys_setting('footer_text');
                $shopName = shop_name();
                $developerName = developer_name();
            @endphp
            <p class="mb-0">
                {{ !empty($footerText) ? $footerText : ("© Copyright " . date('Y') . " " . $shopName . ". All rights reserved.") }}
                <span class="mx-2 text-muted">•</span>
                <span class="text-muted">Developed by <strong class="text-dark">{{ $developerName }}</strong></span>
            </p>
        </div>
    </footer>
    </div>
   
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('assets/js/jquery.slicknav.min.js') }}"></script>

<script src="{{ asset('assets/vendors/charts/morris-bundle/raphael.min.js') }}"></script>
<script src="{{ asset('assets/vendors/charts/morris-bundle/morris.js') }}"></script>

<script src="{{ asset('assets/vendors/charts/charts-bundle/Chart.bundle.js') }}"></script>

<!-- D3 must load BEFORE C3 -->
<script src="{{ asset('assets/vendors/charts/c3charts/d3-5.4.0.min.js') }}"></script>
<script src="{{ asset('assets/vendors/charts/c3charts/c3.min.js') }}"></script>

<!-- DataTables JS with CDN fallback -->
<script src="{{ asset('assets/vendors/data-table/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/data-table/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    if (typeof $.fn.DataTable === 'undefined') {
        document.write('<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"><\/script>');
        document.write('<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"><\/script>');
    }
</script>

<script src="{{ asset('assets/vendors/charts/sparkline/jquery.sparkline.js') }}"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script src="{{ asset('assets/js/home.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>


    <script>
        $(document).ready(function() {
            // Auto-wrap bare tables so they scroll horizontally on small screens
            $('table').each(function() {
                var $t = $(this);
                if ($t.closest('.table-responsive, .pc-tbl-wrap, .dataTables_wrapper, .dt-scroll, .dataTables_scroll, .st-tbl-wrap, .st-table-wrapper, .sl-table-wrapper, .sr-table-wrapper, .pc-tbl-desk, .rp-tbl-wrap, .sa-tbl-wrap').length) return;
                if ($t.attr('id') === 'datatable') return;
                if (!$t.find('tbody tr').length && !$t.find('thead tr').length) return;
                $t.wrap('<div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%;"></div>');
            });

            if (typeof $.fn.DataTable !== 'undefined' && $('#datatable').length) {
                $('#datatable').DataTable({
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    order: [], // 👈 disable DataTables default order
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search.."
                    }
                });
            }
        });
        function showAlert(title, text, icon) {
            Swal.fire({
                title: title,
                html: text,
                icon: icon,
            });
        }


        function logoutAndDeleteFunction(e) {
            var msg = e.getAttribute("data-msg");
            var method = e.getAttribute("data-method");
            var url = e.getAttribute("data-url");

            swal.fire({
                    title: "Are you sure?",
                    text: msg,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'continue',
                    cancelButtonText: 'cancel',
                    dangerMode: true,
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        yourFunction(url, method);
                    } else {
                        swal("Your account is safe!");
                    }
                });

        }

        function yourFunction(url, method) {
            $.ajax({
                url: url,
                type: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response['reload'] != undefined) {
                        showAlert("Success", response.success, "success");
                        window.location.reload();
                    }
                    if (response['redirect'] != undefined) {
                        showAlert("Success", response.success, "success");
                        window.location.href = response['redirect'];
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                }
            });
        }

        function multipleerrorshandle(errors) {
            let message = '';
            for (var errorkey in errors) {
                message += "<span style='color:red'>" + errors[errorkey] + "</span><br>";
            }
            showAlert('Errors', message, 'error');
        }

        function ajaxErrorHandling(data, msg) {
            if (data.hasOwnProperty("responseJSON")) {
                var resp = data.responseJSON;
                if (resp.message == 'CSRF token mismatch.') {
                    showAlert("Page has been expired and will reload in 2 seconds", "Page Expired!", "error");
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                    return;
                }
                if (resp.error) {
                    var msg = (resp.error == '') ? 'Something went wrong!' : resp.error;
                    showAlert(msg, "Error!", "error");
                    return;
                }
                if (resp.message != 'The given data was invalid.') {
                    showAlert(resp.message, "Error!", "error");
                    return;
                }
                multipleerrorshandle(resp.errors);
            } else {
                showAlert(msg + "!", "Error!", 'error');
            }
            return;
        }
        //post
        function myAjax(url, formData, method = 'post', callback) {
            $.ajax({
                url: url,
                method: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                complete: function(data) {},
                success: function(data) {
                    if (data['reload'] != undefined) {
                        showAlert("Success", data.success, "success");
                        window.location.reload();
                        return false;
                    }
                    if (data['redirect'] != undefined) {
                        showAlert("Success", data.success, "success");
                        window.location.href = data['redirect'];
                        return false;
                    }
                    if (data['error'] !== undefined) {
                        var text = "<span style='color:red'>" + data['error'] + "</span>";
                        showAlert('Error', text, 'error');
                        return false;
                    }
                    if (data['errors'] !== undefined) {
                        multipleerrorshandle(data['errors'])
                        return false;
                    }

                    callback(data)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    ajaxErrorHandling(jqXHR, errorThrown);
                },

            });
        }
    </script>

    @if(auth()->check() && auth()->user()->email === 'admin@admin.com')
    <script>
        // Poll for Inward Notifications every 10 seconds
        setInterval(function(){
            $.get("{{ route('notifications.check_inwards') }}", function(data){
                if(data && data.length > 0){
                    
                    let count = data.length;
                    let last = data[0];
                    let vendorName = last.vendor ? last.vendor.name : 'Unknown';
                    let warehouseName = last.warehouse ? last.warehouse.warehouse_name : (last.receive_type == 'shop' ? 'Shop' : 'N/A');
                    
                    let msg = `New Inward (${last.invoice_no || last.id})\nVendor: ${vendorName}\nDest: ${warehouseName}`;
                    
                    // Show Notification
                    Swal.fire({
                        title: 'New Inward Created!',
                        text: msg,
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: true,
                        confirmButtonText: 'Check Now',
                        showCancelButton: true,
                        cancelButtonText: 'Dismiss',
                        timer: 15000,
                        timerProgressBar: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('InwardGatepass.home') }}";
                        }
                    });

                    // Mark as notified immediately to prevent repeat
                    let ids = [];
                    data.forEach(function(item){ ids.push(item.id); });

                    $.ajax({
                        url: "{{ route('notifications.mark_inwards') }}",
                        type: "POST",
                        data: { 
                            ids: ids, 
                            _token: "{{ csrf_token() }}" 
                        },
                        success: function(res){
                            console.log('Marked notified: ', ids);
                        }
                    });
                }
            });
        }, 10000); 
    </script>
    @endif

    @if(auth()->check())
    <script>
        function playTransferChime() {
            try {
                let ctx = new (window.AudioContext || window.webkitAudioContext)();
                let osc = ctx.createOscillator();
                let gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.5);
            } catch(e) {
                console.log('Audio alert playback prevented or unsupported');
            }
        }

        // Poll for Stock Transfer Notifications every 8 seconds for all logged-in branch users
        setInterval(function(){
            $.get("{{ route('notifications.check_transfers') }}", function(data){
                if(data && data.length > 0){
                    playTransferChime();

                    let last = data[0];
                    let from = last.from_warehouse ? last.from_warehouse.warehouse_name : (last.from_branch ? last.from_branch.name : 'Sending Branch');
                    let to = last.to_branch ? last.to_branch.name : (last.to_warehouse ? last.to_warehouse.warehouse_name : 'Shop');
                    
                    let msg = `Dispatched From: ${from}\nTarget: ${to}\nStatus: Pending Acceptance`;
                    
                    let currentBadge = $('#navTransferBadge');
                    if (currentBadge.length) {
                        let curCount = parseInt(currentBadge.text()) || 0;
                        let newCount = curCount + data.length;
                        currentBadge.text(newCount).removeClass('d-none');
                    }

                    Swal.fire({
                        title: '🔔 Incoming Stock Dispatched!',
                        text: msg,
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: true,
                        confirmButtonText: 'View & Accept',
                        showCancelButton: true,
                        cancelButtonText: 'Dismiss',
                        timer: 20000,
                        timerProgressBar: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('stock_transfers.index') }}";
                        }
                    });

                    let ids = [];
                    data.forEach(function(item){ ids.push(item.id); });

                    $.ajax({
                        url: "{{ route('notifications.mark_transfers') }}",
                        type: "POST",
                        data: { 
                            ids: ids, 
                            _token: "{{ csrf_token() }}" 
                        },
                        success: function(res){ 
                            console.log('Marked transfer notified: ', ids); 
                        }
                    });
                }
            });
        }, 8000); 
    </script>
    @endif

    {{-- Global Premium Page Transition & Progress Bar --}}
    <div id="erpTopProgress" aria-hidden="true"></div>
    <script>
        (function() {
            var bar = document.getElementById('erpTopProgress');
            if (!bar) return;

            var activeCount = 0;
            var trickleTimer = null;
            var currentWidth = 0;

            function startProgress() {
                activeCount++;
                if (activeCount > 1) return;
                clearInterval(trickleTimer);
                currentWidth = 18;
                bar.style.width = currentWidth + '%';
                bar.classList.add('is-active');
                trickleTimer = setInterval(function() {
                    if (currentWidth < 86) {
                        currentWidth += Math.max(1, (88 - currentWidth) * 0.14);
                        bar.style.width = currentWidth.toFixed(1) + '%';
                    }
                }, 160);
            }

            function finishProgress(force) {
                if (force) activeCount = 0;
                else activeCount = Math.max(0, activeCount - 1);
                if (activeCount > 0) return;
                clearInterval(trickleTimer);
                bar.style.width = '100%';
                setTimeout(function() {
                    bar.classList.remove('is-active');
                    setTimeout(function() {
                        if (activeCount === 0) bar.style.width = '0%';
                    }, 220);
                }, 140);
            }

            // Reset state when navigating back via browser history cache
            window.addEventListener('pageshow', function() {
                document.body.classList.remove('erp-page-leaving');
                finishProgress(true);
            });

            // Ensure Bootstrap modals always render at body level (screen-centered, above backdrop)
            if (window.jQuery) {
                $(document).on('show.bs.modal', '.modal', function() {
                    if (this.parentElement !== document.body) {
                        document.body.appendChild(this);
                    }
                });
            } else {
                document.addEventListener('show.bs.modal', function(e) {
                    if (e.target && e.target.classList && e.target.classList.contains('modal') && e.target.parentElement !== document.body) {
                        document.body.appendChild(e.target);
                    }
                });
            }

            // Smooth exit + progress bar on internal link clicks
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a');
                if (!link) return;
                if (e.defaultPrevented || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

                var href = link.getAttribute('href') || '';
                if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return;
                if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
                if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-toggle')) return;
                if (link.classList.contains('no-loader')) return;

                try {
                    var targetUrl = new URL(link.href, window.location.origin);
                    if (targetUrl.origin !== window.location.origin) return;
                } catch (err) {
                    return;
                }

                document.body.classList.add('erp-page-leaving');
                startProgress();
            });

            // Show progress bar on standard form submissions
            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form || e.defaultPrevented) return;
                if (form.getAttribute('target') === '_blank' || form.classList.contains('no-loader')) return;
                startProgress();
            });

            // Hook jQuery AJAX (skip silent background notification polling)
            if (window.jQuery) {
                $(document).ajaxSend(function(_evt, _xhr, settings) {
                    var url = (settings && settings.url) ? String(settings.url) : '';
                    if (url.indexOf('notifications/check') !== -1 || url.indexOf('notifications/mark') !== -1) return;
                    startProgress();
                });
                $(document).ajaxComplete(function(_evt, _xhr, settings) {
                    var url = (settings && settings.url) ? String(settings.url) : '';
                    if (url.indexOf('notifications/check') !== -1 || url.indexOf('notifications/mark') !== -1) return;
                    finishProgress();
                });
            }

            // Hook window.fetch (used by 360 Report and modern endpoints)
            if (window.fetch) {
                var origFetch = window.fetch;
                window.fetch = function() {
                    var reqUrl = arguments[0] ? String(arguments[0].url || arguments[0]) : '';
                    var isSilent = reqUrl.indexOf('notifications/check') !== -1 || reqUrl.indexOf('notifications/mark') !== -1;
                    if (!isSilent) startProgress();
                    return origFetch.apply(this, arguments).then(function(res) {
                        if (!isSilent) finishProgress();
                        return res;
                    }, function(err) {
                        if (!isSilent) finishProgress();
                        throw err;
                    });
                };
            }
        })();
    </script>

