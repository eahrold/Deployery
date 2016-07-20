/**
 * vue-resource v0.7.0
 * https://github.com/vuejs/vue-resource
 * Released under the MIT License.
 */

!function(t, e) {
    "object" == typeof exports && "object" == typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define([], e) : "object" == typeof exports ? exports.VueResource = e() : t.VueResource = e()
}(this, function() {
    return function(t) {
        function e(r) {
            if (n[r])
                return n[r].exports;
            var o = n[r] = {
                exports: {},
                id: r,
                loaded: !1
            };
            return t[r].call(o.exports, o, o.exports, e), o.loaded=!0, o.exports
        }
        var n = {};
        return e.m = t, e.c = n, e.p = "", e(0)
    }([function(t, e, n) {
        function r(t) {
            var e = n(1);
            e.config = t.config, e.warning = t.util.warn, e.nextTick = t.util.nextTick, t.url = n(2), t.http = n(8), t.resource = n(23), t.Promise = n(10), Object.defineProperties(t.prototype, {
                $url: {
                    get: function() {
                        return e.options(t.url, this, this.$options.url)
                    }
                },
                $http: {
                    get: function() {
                        return e.options(t.http, this, this.$options.http)
                    }
                },
                $resource: {
                    get: function() {
                        return t.resource.bind(this)
                    }
                },
                $promise: {
                    get: function() {
                        return function(e) {
                            return new t.Promise(e, this)
                        }.bind(this)
                    }
                }
            })
        }
        window.Vue && Vue.use(r), t.exports = r
    }, function(t, e) {
        function n(t, e, o) {
            for (var i in e)
                o && (r.isPlainObject(e[i]) || r.isArray(e[i])) ? (r.isPlainObject(e[i])&&!r.isPlainObject(t[i]) && (t[i] = {}), r.isArray(e[i])&&!r.isArray(t[i]) && (t[i] = []), n(t[i], e[i], o)) : void 0 !== e[i] && (t[i] = e[i])
        }
        var r = e, o = [], i = window.console;
        r.warn = function(t) {
            i && r.warning && (!r.config.silent || r.config.debug) && i.warn("[VueResource warn]: " + t)
        }, r.error = function(t) {
            i && i.error(t)
        }, r.trim = function(t) {
            return t.replace(/^\s*|\s*$/g, "")
        }, r.toLower = function(t) {
            return t ? t.toLowerCase() : ""
        }, r.isArray = Array.isArray, r.isString = function(t) {
            return "string" == typeof t
        }, r.isFunction = function(t) {
            return "function" == typeof t
        }, r.isObject = function(t) {
            return null !== t && "object" == typeof t
        }, r.isPlainObject = function(t) {
            return r.isObject(t) && Object.getPrototypeOf(t) == Object.prototype
        }, r.options = function(t, e, n) {
            return n = n || {}, r.isFunction(n) && (n = n.call(e)), r.merge(t.bind({
                $vm: e,
                $options: n
            }), t, {
                $options: n
            })
        }, r.each = function(t, e) {
            var n, o;
            if ("number" == typeof t.length)
                for (n = 0; n < t.length; n++)
                    e.call(t[n], t[n], n);
            else if (r.isObject(t))
                for (o in t)
                    t.hasOwnProperty(o) && e.call(t[o], t[o], o);
            return t
        }, r.defaults = function(t, e) {
            for (var n in e)
                void 0 === t[n] && (t[n] = e[n]);
            return t
        }, r.extend = function(t) {
            var e = o.slice.call(arguments, 1);
            return e.forEach(function(e) {
                n(t, e)
            }), t
        }, r.merge = function(t) {
            var e = o.slice.call(arguments, 1);
            return e.forEach(function(e) {
                n(t, e, !0)
            }), t
        }
    }, function(t, e, n) {
        function r(t, e) {
            var n, i = t;
            return s.isString(t) && (i = {
                url: t,
                params: e
            }), i = s.merge({}, r.options, this.$options, i), r.transforms.forEach(function(t) {
                n = o(t, n, this.$vm)
            }, this), n(i)
        }
        function o(t, e, n) {
            return function(r) {
                return t.call(n, r, e)
            }
        }
        function i(t, e, n) {
            var r, o = s.isArray(e), a = s.isPlainObject(e);
            s.each(e, function(e, u) {
                r = s.isObject(e) || s.isArray(e), n && (u = n + "[" + (a || r ? u : "") + "]"), !n && o ? t.add(e.name, e.value) : r ? i(t, e, u) : t.add(u, e)
            })
        }
        var s = n(1), a = document.documentMode, u = document.createElement("a");
        r.options = {
            url: "",
            root: null,
            params: {}
        }, r.transforms = [n(3), n(5), n(6), n(7)], r.params = function(t) {
            var e = [], n = encodeURIComponent;
            return e.add = function(t, e) {
                s.isFunction(e) && (e = e()), null === e && (e = ""), this.push(n(t) + "=" + n(e))
            }, i(e, t), e.join("&").replace(/%20/g, "+")
        }, r.parse = function(t) {
            return a && (u.href = t, t = u.href), u.href = t, {
                href: u.href,
                protocol: u.protocol ? u.protocol.replace(/:$/, ""): "",
                port: u.port,
                host: u.host,
                hostname: u.hostname,
                pathname: "/" === u.pathname.charAt(0) ? u.pathname: "/" + u.pathname,
                search: u.search ? u.search.replace(/^\?/, ""): "",
                hash: u.hash ? u.hash.replace(/^#/, ""): ""
            }
        }, t.exports = s.url = r
    }, function(t, e, n) {
        var r = n(4);
        t.exports = function(t) {
            var e = [], n = r.expand(t.url, t.params, e);
            return e.forEach(function(e) {
                delete t.params[e]
            }), n
        }
    }, function(t, e) {
        e.expand = function(t, e, n) {
            var r = this.parse(t), o = r.expand(e);
            return n && n.push.apply(n, r.vars), o
        }, e.parse = function(t) {
            var n = ["+", "#", ".", "/", ";", "?", "&"], r = [];
            return {
                vars: r,
                expand: function(o) {
                    return t.replace(/\{([^\{\}]+)\}|([^\{\}]+)/g, function(t, i, s) {
                        if (i) {
                            var a = null, u = [];
                            if ( - 1 !== n.indexOf(i.charAt(0)) && (a = i.charAt(0), i = i.substr(1)), i.split(/,/g).forEach(function(t) {
                                var n = /([^:\*]*)(?::(\d+)|(\*))?/.exec(t);
                                u.push.apply(u, e.getValues(o, a, n[1], n[2] || n[3])), r.push(n[1])
                            }), a && "+" !== a) {
                                var c = ",";
                                return "?" === a ? c = "&" : "#" !== a && (c = a), (0 !== u.length ? a : "") + u.join(c)
                            }
                            return u.join(",")
                        }
                        return e.encodeReserved(s)
                    })
                }
            }
        }, e.getValues = function(t, e, n, r) {
            var o = t[n], i = [];
            if (this.isDefined(o) && "" !== o)
                if ("string" == typeof o || "number" == typeof o || "boolean" == typeof o)
                    o = o.toString(), r && "*" !== r && (o = o.substring(0, parseInt(r, 10))), i.push(this.encodeValue(e, o, this.isKeyOperator(e) ? n : null));
                else if ("*" === r)
                    Array.isArray(o) ? o.filter(this.isDefined).forEach(function(t) {
                        i.push(this.encodeValue(e, t, this.isKeyOperator(e) ? n : null))
                    }, this) : Object.keys(o).forEach(function(t) {
                        this.isDefined(o[t]) && i.push(this.encodeValue(e, o[t], t))
                    }, this);
                else {
                    var s = [];
                    Array.isArray(o) ? o.filter(this.isDefined).forEach(function(t) {
                        s.push(this.encodeValue(e, t))
                    }, this) : Object.keys(o).forEach(function(t) {
                        this.isDefined(o[t]) && (s.push(encodeURIComponent(t)), s.push(this.encodeValue(e, o[t].toString())))
                    }, this), this.isKeyOperator(e) ? i.push(encodeURIComponent(n) + "=" + s.join(",")) : 0 !== s.length && i.push(s.join(","))
                } else 
                    ";" === e ? i.push(encodeURIComponent(n)) : "" !== o || "&" !== e && "?" !== e ? "" === o && i.push("") : i.push(encodeURIComponent(n) + "=");
            return i
        }, e.isDefined = function(t) {
            return void 0 !== t && null !== t
        }, e.isKeyOperator = function(t) {
            return ";" === t || "&" === t || "?" === t
        }, e.encodeValue = function(t, e, n) {
            return e = "+" === t || "#" === t ? this.encodeReserved(e) : encodeURIComponent(e), n ? encodeURIComponent(n) + "=" + e : e
        }, e.encodeReserved = function(t) {
            return t.split(/(%[0-9A-Fa-f]{2})/g).map(function(t) {
                return /%[0-9A-Fa-f]/.test(t) || (t = encodeURI(t)), t
            }).join("")
        }
    }, function(t, e, n) {
        function r(t) {
            return o(t, !0).replace(/%26/gi, "&").replace(/%3D/gi, "=").replace(/%2B/gi, "+")
        }
        function o(t, e) {
            return encodeURIComponent(t).replace(/%40/gi, "@").replace(/%3A/gi, ":").replace(/%24/g, "$").replace(/%2C/gi, ",").replace(/%20/g, e ? "%20" : "+")
        }
        var i = n(1);
        t.exports = function(t, e) {
            var n = [], o = e(t);
            return o = o.replace(/(\/?):([a-z]\w*)/gi, function(e, o, s) {
                return i.warn("The `:" + s + "` parameter syntax has been deprecated. Use the `{" + s + "}` syntax instead."), t.params[s] ? (n.push(s), o + r(t.params[s])) : ""
            }), n.forEach(function(e) {
                delete t.params[e]
            }), o
        }
    }, function(t, e, n) {
        var r = n(1);
        t.exports = function(t, e) {
            var n = Object.keys(r.url.options.params), o = {}, i = e(t);
            return r.each(t.params, function(t, e) {
                - 1 === n.indexOf(e) && (o[e] = t)
            }), o = r.url.params(o), o && (i += ( - 1 == i.indexOf("?") ? "?" : "&") + o), i
        }
    }, function(t, e, n) {
        var r = n(1);
        t.exports = function(t, e) {
            var n = e(t);
            return r.isString(t.root)&&!n.match(/^(https?:)?\//) && (n = t.root + "/" + n), n
        }
    }, function(t, e, n) {
        function r(t, e) {
            var n, u, c = i;
            return r.interceptors.forEach(function(t) {
                c = a(t, this.$vm)(c)
            }, this), e = o.isObject(t) ? t : o.extend({
                url: t
            }, e), n = o.merge({}, r.options, this.$options, e), u = c(n).bind(this.$vm).then(function(t) {
                return t.ok ? t : s.reject(t)
            }, function(t) {
                return t instanceof Error && o.error(t), s.reject(t)
            }), n.success && u.success(n.success), n.error && u.error(n.error), u
        }
        var o = n(1), i = n(9), s = n(10), a = n(13), u = {
            "Content-Type": "application/json"
        };
        r.options = {
            method: "get",
            data: "",
            params: {},
            headers: {},
            xhr: null,
            upload: null,
            jsonp: "callback",
            beforeSend: null,
            crossOrigin: null,
            emulateHTTP: !1,
            emulateJSON: !1,
            timeout: 0
        }, r.interceptors = [n(14), n(15), n(16), n(18), n(19), n(20), n(21)], r.headers = {
            put: u,
            post: u,
            patch: u,
            "delete": u,
            common: {
                Accept: "application/json, text/plain, */*"
            },
            custom: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }, ["get", "put", "post", "patch", "delete", "jsonp"].forEach(function(t) {
            r[t] = function(e, n, r, i) {
                return o.isFunction(n) && (i = r, r = n, n = void 0), o.isObject(r) && (i = r, r = void 0), this (e, o.extend({
                    method: t,
                    data: n,
                    success: r
                }, i))
            }
        }), t.exports = o.http = r
    }, function(t, e, n) {
        function r(t) {
            var e, n, r, i = {};
            return o.isString(t) && o.each(t.split("\n"), function(t) {
                r = t.indexOf(":"), n = o.trim(o.toLower(t.slice(0, r))), e = o.trim(t.slice(r + 1)), i[n] ? o.isArray(i[n]) ? i[n].push(e) : i[n] = [i[n], e] : i[n] = e
            }), i
        }
        var o = n(1), i = n(10), s = n(12);
        t.exports = function(t) {
            var e = (t.client || s)(t);
            return i.resolve(e).then(function(t) {
                if (t.headers) {
                    var e = r(t.headers);
                    t.headers = function(t) {
                        return t ? e[o.toLower(t)] : e
                    }
                }
                return t.ok = t.status >= 200 && t.status < 300, t
            })
        }
    }, function(t, e, n) {
        function r(t, e) {
            t instanceof i ? this.promise = t : this.promise = new i(t.bind(e)), this.context = e
        }
        var o = n(1), i = window.Promise || n(11);
        r.all = function(t, e) {
            return new r(i.all(t), e)
        }, r.resolve = function(t, e) {
            return new r(i.resolve(t), e)
        }, r.reject = function(t, e) {
            return new r(i.reject(t), e)
        }, r.race = function(t, e) {
            return new r(i.race(t), e)
        };
        var s = r.prototype;
        s.bind = function(t) {
            return this.context = t, this
        }, s.then = function(t, e) {
            return t && t.bind && this.context && (t = t.bind(this.context)), e && e.bind && this.context && (e = e.bind(this.context)), this.promise = this.promise.then(t, e), this
        }, s["catch"] = function(t) {
            return t && t.bind && this.context && (t = t.bind(this.context)), this.promise = this.promise["catch"](t), this
        }, s["finally"] = function(t) {
            return this.then(function(e) {
                return t.call(this), e
            }, function(e) {
                return t.call(this), i.reject(e)
            })
        }, s.success = function(t) {
            return o.warn("The `success` method has been deprecated. Use the `then` method instead."), this.then(function(e) {
                return t.call(this, e.data, e.status, e) || e
            })
        }, s.error = function(t) {
            return o.warn("The `error` method has been deprecated. Use the `catch` method instead."), this["catch"](function(e) {
                return t.call(this, e.data, e.status, e) || e
            })
        }, s.always = function(t) {
            o.warn("The `always` method has been deprecated. Use the `finally` method instead.");
            var e = function(e) {
                return t.call(this, e.data, e.status, e) || e
            };
            return this.then(e, e)
        }, t.exports = r
    }, function(t, e, n) {
        function r(t) {
            this.state = a, this.value = void 0, this.deferred = [];
            var e = this;
            try {
                t(function(t) {
                    e.resolve(t)
                }, function(t) {
                    e.reject(t)
                })
            } catch (n) {
                e.reject(n)
            }
        }
        var o = n(1), i = 0, s = 1, a = 2;
        r.reject = function(t) {
            return new r(function(e, n) {
                n(t)
            })
        }, r.resolve = function(t) {
            return new r(function(e, n) {
                e(t)
            })
        }, r.all = function(t) {
            return new r(function(e, n) {
                function o(n) {
                    return function(r) {
                        s[n] = r, i += 1, i === t.length && e(s)
                    }
                }
                var i = 0, s = [];
                0 === t.length && e(s);
                for (var a = 0; a < t.length; a += 1)
                    r.resolve(t[a]).then(o(a), n)
            })
        }, r.race = function(t) {
            return new r(function(e, n) {
                for (var o = 0; o < t.length; o += 1)
                    r.resolve(t[o]).then(e, n)
            })
        };
        var u = r.prototype;
        u.resolve = function(t) {
            var e = this;
            if (e.state === a) {
                if (t === e)
                    throw new TypeError("Promise settled with itself.");
                var n=!1;
                try {
                    var r = t && t.then;
                    if (null !== t && "object" == typeof t && "function" == typeof r)
                        return void r.call(t, function(t) {
                        n || e.resolve(t), n=!0
                    }, function(t) {
                        n || e.reject(t), n=!0
                    })
                } catch (o) {
                    return void(n || e.reject(o))
                }
                e.state = i, e.value = t, e.notify()
            }
        }, u.reject = function(t) {
            var e = this;
            if (e.state === a) {
                if (t === e)
                    throw new TypeError("Promise settled with itself.");
                e.state = s, e.value = t, e.notify()
            }
        }, u.notify = function() {
            var t = this;
            o.nextTick(function() {
                if (t.state !== a)
                    for (; t.deferred.length;) {
                        var e = t.deferred.shift(), n = e[0], r = e[1], o = e[2], u = e[3];
                        try {
                            t.state === i ? o("function" == typeof n ? n.call(void 0, t.value) : t.value) : t.state === s && ("function" == typeof r ? o(r.call(void 0, t.value)) : u(t.value))
                        } catch (c) {
                            u(c)
                        }
                    }
            })
        }, u.then = function(t, e) {
            var n = this;
            return new r(function(r, o) {
                n.deferred.push([t, e, r, o]), n.notify()
            })
        }, u["catch"] = function(t) {
            return this.then(void 0, t)
        }, t.exports = r
    }, function(t, e, n) {
        var r = n(1), o = n(10);
        t.exports = function(t) {
            return new o(function(e) {
                var n, o = new XMLHttpRequest, i = {
                    request: t
                };
                t.cancel = function() {
                    o.abort()
                }, o.open(t.method, r.url(t), !0), n = function(t) {
                    i.data = o.responseText, i.status = o.status, i.statusText = o.statusText, i.headers = o.getAllResponseHeaders(), e(i)
                }, o.timeout = 0, o.onload = n, o.onabort = n, o.onerror = n, o.ontimeout = function() {}, o.onprogress = function() {}, r.isPlainObject(t.xhr) && r.extend(o, t.xhr), r.isPlainObject(t.upload) && r.extend(o.upload, t.upload), r.each(t.headers || {}, function(t, e) {
                    o.setRequestHeader(e, t)
                }), o.send(t.data)
            })
        }
    }, function(t, e, n) {
        function r(t, e, n) {
            var r = i.resolve(t);
            return arguments.length < 2 ? r : r.then(e, n)
        }
        var o = n(1), i = n(10);
        t.exports = function(t, e) {
            return function(n) {
                return o.isFunction(t) && (t = t.call(e, i)), function(i) {
                    return o.isFunction(t.request) && (i = t.request.call(e, i)), r(i, function(i) {
                        return r(n(i), function(n) {
                            return o.isFunction(t.response) && (n = t.response.call(e, n)), n
                        })
                    })
                }
            }
        }
    }, function(t, e, n) {
        var r = n(1);
        t.exports = {
            request: function(t) {
                return r.isFunction(t.beforeSend) && t.beforeSend.call(this, t), t
            }
        }
    }, function(t, e) {
        t.exports = function() {
            var t;
            return {
                request: function(e) {
                    return e.timeout && (t = setTimeout(function() {
                        e.cancel()
                    }, e.timeout)), e
                },
                response: function(e) {
                    return clearTimeout(t), e
                }
            }
        }
    }, function(t, e, n) {
        var r = n(17);
        t.exports = {
            request: function(t) {
                return "JSONP" == t.method && (t.client = r), t
            }
        }
    }, function(t, e, n) {
        var r = n(1), o = n(10);
        t.exports = function(t) {
            return new o(function(e) {
                var n, o, i = "_jsonp" + Math.random().toString(36).substr(2), s = {
                    request: t,
                    data: null
                };
                t.params[t.jsonp] = i, t.cancel = function() {
                    n({
                        type: "cancel"
                    })
                }, o = document.createElement("script"), o.src = r.url(t), o.type = "text/javascript", o.async=!0, window[i] = function(t) {
                    s.data = t
                }, n = function(t) {
                    "load" === t.type && null !== s.data ? s.status = 200 : "error" === t.type ? s.status = 404 : s.status = 0, e(s), delete window[i], document.body.removeChild(o)
                }, o.onload = n, o.onerror = n, document.body.appendChild(o)
            })
        }
    }, function(t, e) {
        t.exports = {
            request: function(t) {
                return t.emulateHTTP && /^(PUT|PATCH|DELETE)$/i.test(t.method) && (t.headers["X-HTTP-Method-Override"] = t.method, t.method = "POST"), t
            }
        }
    }, function(t, e, n) {
        var r = n(1);
        t.exports = {
            request: function(t) {
                return t.emulateJSON && r.isPlainObject(t.data) && (t.headers["Content-Type"] = "application/x-www-form-urlencoded", t.data = r.url.params(t.data)), r.isObject(t.data) && /FormData/i.test(t.data.toString()) && delete t.headers["Content-Type"], r.isPlainObject(t.data) && (t.data = JSON.stringify(t.data)), t
            },
            response: function(t) {
                try {
                    t.data = JSON.parse(t.data)
                } catch (e) {}
                return t
            }
        }
    }, function(t, e, n) {
        var r = n(1);
        t.exports = {
            request: function(t) {
                return t.method = t.method.toUpperCase(), t.headers = r.extend({}, r.http.headers.common, t.crossOrigin ? {} : r.http.headers.custom, r.http.headers[t.method.toLowerCase()], t.headers), r.isPlainObject(t.data) && /^(GET|JSONP)$/i.test(t.method) && (r.extend(t.params, t.data), delete t.data), t
            }
        }
    }, function(t, e, n) {
        function r(t) {
            var e = o.url.parse(o.url(t));
            return e.protocol !== a.protocol || e.host !== a.host
        }
        var o = n(1), i = n(22), s = "withCredentials"in new XMLHttpRequest, a = o.url.parse(location.href);
        t.exports = {
            request: function(t) {
                return null === t.crossOrigin && (t.crossOrigin = r(t)), t.crossOrigin && (s || (t.client = i), t.emulateHTTP=!1), t
            }
        }
    }, function(t, e, n) {
        var r = n(1), o = n(10);
        t.exports = function(t) {
            return new o(function(e) {
                var n, o = new XDomainRequest, i = {
                    request: t
                };
                t.cancel = function() {
                    o.abort()
                }, o.open(t.method, r.url(t), !0), n = function(t) {
                    i.data = o.responseText, i.status = o.status, i.statusText = o.statusText, e(i)
                }, o.timeout = 0, o.onload = n, o.onabort = n, o.onerror = n, o.ontimeout = function() {}, o.onprogress = function() {}, o.send(t.data)
            })
        }
    }, function(t, e, n) {
        function r(t, e, n, s) {
            var a = this, u = {};
            return n = i.extend({}, r.actions, n), i.each(n, function(n, r) {
                n = i.merge({
                    url: t,
                    params: e || {}
                }, s, n), u[r] = function() {
                    return (a.$http || i.http)(o(n, arguments))
                }
            }), u
        }
        function o(t, e) {
            var n, r, o, s = i.extend({}, t), a = {};
            switch (e.length) {
            case 4:
                o = e[3], r = e[2];
            case 3:
            case 2:
                if (!i.isFunction(e[1])) {
                    a = e[0], n = e[1], r = e[2];
                    break
                }
                if (i.isFunction(e[0])) {
                    r = e[0], o = e[1];
                    break
                }
                r = e[1], o = e[2];
            case 1:
                i.isFunction(e[0]) ? r = e[0] : /^(POST|PUT|PATCH)$/i.test(s.method) ? n = e[0] : a = e[0];
                break;
            case 0:
                break;
            default:
                throw "Expected up to 4 arguments [params, data, success, error], got " + e.length + " arguments"
            }
            return s.data = n, s.params = i.extend({}, s.params, a), r && (s.success = r), o && (s.error = o), s
        }
        var i = n(1);
        r.actions = {
            get: {
                method: "GET"
            },
            save: {
                method: "POST"
            },
            query: {
                method: "GET"
            },
            update: {
                method: "PUT"
            },
            remove: {
                method: "DELETE"
            },
            "delete": {
                method: "DELETE"
            }
        }, t.exports = i.resource = r
    }
    ])
});

