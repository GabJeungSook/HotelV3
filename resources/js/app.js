import './bootstrap'

import autoAnimate from '@formkit/auto-animate'
import collapse from '@alpinejs/collapse'
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm'

Alpine.plugin(collapse)
Alpine.directive('animate', (el) => {
    autoAnimate(el)
})

Livewire.start()
