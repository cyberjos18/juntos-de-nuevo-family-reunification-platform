{{-- Declaración de Propósito — Modal de aceptación --}}

<style>[x-cloak]{display:none!important}</style>

<div x-data="{ show: false, accepted: false }"
     x-init="show = document.cookie.indexOf('declaracion_aceptada') === -1;"
     x-cloak>

    {{-- UN SOLO BLOQUE: overlay padre + modal hijo --}}
    <div x-show="show"
         x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6"
         style="background: rgba(0,0,0,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"
         @keydown.escape.window="if(accepted){document.cookie='declaracion_aceptada=true;path=/;max-age=31536000';show=false}"
         @click.self="if(accepted){document.cookie='declaracion_aceptada=true;path=/;max-age=31536000';show=false}">

        {{-- MODAL CARD --}}
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col"
             style="max-height: 88vh;"
             @click.stop>

            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-6 sm:px-8 pt-8 sm:pt-10 pb-5 sm:pb-6 text-center shrink-0">
                <span class="material-symbols-outlined text-5xl sm:text-6xl text-white/90 block mx-auto mb-2">gavel</span>
                <h2 class="text-xl sm:text-2xl font-bold text-white leading-tight">Declaración de propósito y uso responsable</h2>
                <p class="text-blue-100 text-sm mt-1">Por favor, lee y acepta antes de continuar</p>
            </div>

            {{-- BODY (scrollable) --}}
            <div class="flex-1 overflow-y-auto px-6 sm:px-10 pt-8 sm:pt-10 pb-10 sm:pb-12 text-gray-700 leading-relaxed text-sm sm:text-base space-y-3 sm:space-y-4"
                 style="-webkit-overflow-scrolling: touch;">

                <p>De un venezolano a otro, quiero decirte que lamento profundamente lo sucedido.</p>

                <p>Soy ingeniero informático, con más de 10 años de experiencia, y durante las últimas noches he trabajado en esta plataforma pensando en todos esos niños, niñas y adolescentes que pueden estar en centros de acogida, refugios o bajo el cuidado temporal de otras personas, mientras sus familiares no saben dónde buscar o por dónde empezar.</p>

                <p>Por esa razón decidí aportar desde lo que sé hacer y crear <strong>Juntos de Nuevo</strong>, una plataforma pensada para ayudar a reunir familias en momentos de emergencia.</p>

                <p>La idea de este proyecto es funcionar como un punto de información y registro responsable, donde los centros de acogida o personas autorizadas puedan publicar información sobre niños, niñas y adolescentes que se encuentren bajo resguardo, con el objetivo de facilitar que sus familiares puedan ubicarlos de forma más rápida y organizada.</p>

                <p>Además, cuando un familiar, representante, amigo o conocido se acerque a buscar a un niño o niña, la plataforma busca mantener un registro básico de quién realizó la solicitud, cuándo fue atendida y bajo qué condiciones se produjo el retiro o reencuentro. Esto tiene como finalidad aportar mayor seguridad, trazabilidad y protección para los menores.</p>

                <p>Mi intención con esta iniciativa es ayudar en la mayor medida posible. No obtengo ningún beneficio económico, no cobro por el uso de la plataforma y no genero ganancia alguna con este proyecto.</p>

                <p>Esta plataforma ha sido creada de forma independiente y con fines humanitarios. No sustituye el trabajo de las autoridades, organismos de protección infantil, centros de acogida, cuerpos de seguridad, organizaciones humanitarias ni instituciones competentes. Su propósito es servir como una herramienta de apoyo para facilitar información, organización y posibles reencuentros familiares.</p>

                <p>Por limitaciones personales y económicas, solo podré garantizar el mantenimiento de esta plataforma durante un período inicial de <strong>un año</strong>. Espero sinceramente que durante ese tiempo pueda ser de utilidad para quienes más lo necesitan.</p>

                <p>Gracias por usar esta herramienta con responsabilidad, respeto y sensibilidad.</p>

                <div class="border-l-4 border-blue-600 bg-blue-50 rounded-r-lg px-5 py-4 mt-6">
                    <p class="text-gray-900 font-bold text-lg">Juntos de Nuevo</p>
                    <p class="text-gray-600 text-sm mt-1">Una plataforma para ayudar a reunir familias.</p>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="bg-gray-50 border-t-2 border-gray-200 px-6 sm:px-8 pt-6 sm:pt-8 pb-6 sm:pb-8 shrink-0">

                <label class="flex items-start gap-3 cursor-pointer mb-6">
                    <input type="checkbox"
                           x-model="accepted"
                           class="w-5 h-5 mt-0.5 rounded border-2 border-gray-300 text-primary focus:ring-primary cursor-pointer flex-shrink-0">
                    <span class="text-sm text-on-surface-variant leading-snug">
                        Al continuar, aceptas usar esta plataforma <strong class="text-on-surface">únicamente con fines humanitarios</strong>, de búsqueda familiar y protección de niños, niñas y adolescentes. Cualquier uso indebido, falso, malintencionado o que ponga en riesgo a un menor podrá ser <strong class="text-on-surface">reportado y bloqueado</strong>.
                    </span>
                </label>

                <button type="button"
                        @click="if(accepted){document.cookie='declaracion_aceptada=true;path=/;max-age=31536000';show=false}"
                        :disabled="!accepted"
                        class="w-full py-4 rounded-full font-semibold text-base sm:text-lg transition-all duration-200 inline-flex items-center justify-center gap-2 bg-primary text-on-primary hover:brightness-110 shadow-lg hover:shadow-xl disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:shadow-none disabled:hover:shadow-none disabled:hover:brightness-100">
                    <span class="material-symbols-outlined text-xl" :class="accepted ? 'opacity-100' : 'opacity-40'">verified</span>
                    Aceptar Declaración
                </button>
            </div>

        </div>
    </div>
</div>
