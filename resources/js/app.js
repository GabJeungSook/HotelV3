import './bootstrap'

import autoAnimate from '@formkit/auto-animate'
import collapse from '@alpinejs/collapse'
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm'

Alpine.plugin(collapse)
Alpine.directive('animate', (el) => {
    autoAnimate(el)
})

window.countdown = function (checkoutIso) {
    return {
        state: 'active',
        display: '',
        interval: null,
        start() {
            const checkoutTime = new Date(checkoutIso).getTime()
            const graceEnd = checkoutTime + 15 * 60 * 1000
            const tick = () => {
                const now = Date.now()
                if (now > graceEnd) {
                    this.state = 'expired'
                    this.display = ''
                    clearInterval(this.interval)
                } else if (now > checkoutTime) {
                    this.state = 'grace'
                    this.display = this.format(graceEnd - now)
                } else {
                    this.state = 'active'
                    this.display = this.format(checkoutTime - now)
                }
            }
            tick()
            this.interval = setInterval(tick, 1000)
        },
        format(ms) {
            const d = Math.floor(ms / 86400000)
            const h = Math.floor((ms % 86400000) / 3600000)
            const m = Math.floor((ms % 3600000) / 60000)
            const s = Math.floor((ms % 60000) / 1000)
            const pad = n => String(n).padStart(2, '0')
            return d > 0
                ? `${d}d : ${pad(h)}h : ${pad(m)}m : ${pad(s)}s`
                : `${pad(h)}h : ${pad(m)}m : ${pad(s)}s`
        },
        destroy() {
            if (this.interval) clearInterval(this.interval)
        }
    }
}

Livewire.start()
