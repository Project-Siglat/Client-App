
// this file is generated — do not edit it


declare module "svelte/elements" {
	export interface HTMLAttributes<T> {
		'data-sveltekit-keepfocus'?: true | '' | 'off' | undefined | null;
		'data-sveltekit-noscroll'?: true | '' | 'off' | undefined | null;
		'data-sveltekit-preload-code'?:
			| true
			| ''
			| 'eager'
			| 'viewport'
			| 'hover'
			| 'tap'
			| 'off'
			| undefined
			| null;
		'data-sveltekit-preload-data'?: true | '' | 'hover' | 'tap' | 'off' | undefined | null;
		'data-sveltekit-reload'?: true | '' | 'off' | undefined | null;
		'data-sveltekit-replacestate'?: true | '' | 'off' | undefined | null;
	}
}

export {};


declare module "$app/types" {
	export interface AppTypes {
		RouteId(): "/" | "/admin" | "/ambulance" | "/client" | "/login" | "/{client,admin,ambulance}";
		RouteParams(): {
			
		};
		LayoutParams(): {
			"/": Record<string, never>;
			"/admin": Record<string, never>;
			"/ambulance": Record<string, never>;
			"/client": Record<string, never>;
			"/login": Record<string, never>;
			"/{client,admin,ambulance}": Record<string, never>
		};
		Pathname(): "/" | "/admin" | "/admin/" | "/ambulance" | "/ambulance/" | "/client" | "/client/" | "/login" | "/login/" | "/{client,admin,ambulance}" | "/{client,admin,ambulance}/";
		ResolvedPathname(): `${"" | `/${string}`}${ReturnType<AppTypes['Pathname']>}`;
		Asset(): "/images/aid.png" | "/images/ambulance.png" | "/images/brave.png" | "/images/firefox.png" | "/images/man.svg" | "/images/pin.png" | "/images/siglat.png" | string & {};
	}
}