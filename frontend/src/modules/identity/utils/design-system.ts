import type { BadgeVariant } from '../types/auth.types';

export const BADGE_VARIANT_CLASSES: Readonly<Record<BadgeVariant, string>> = Object.freeze({
  forest: 'bg-[#2D5A3F] text-[#F9F6F0]',
  emerald: 'bg-[#E9F2EB] text-[#2D5A3F] border border-[#2D5A3F]/30',
  copper: 'bg-[#F9F3EA] text-[#B87333] border border-[#B87333]/30',
  crimson: 'bg-[#F9EDED] text-[#8C433E] border border-[#8C433E]/30',
  stone: 'bg-[#EBE5D8] text-[#4A584E]',
  amber: 'bg-[#FFF8E7] text-[#C27D38] border border-[#C27D38]/30',
  indigo: 'bg-[#EEF2FF] text-[#4F46E5] border border-[#4F46E5]/30'
});

/**
 * Returns the CSS class for a given design system badge variant.
 */
export function getBadgeClassByVariant(variant?: BadgeVariant | string | null): string {
  if (variant && variant in BADGE_VARIANT_CLASSES) {
    return BADGE_VARIANT_CLASSES[variant as BadgeVariant];
  }
  return BADGE_VARIANT_CLASSES.stone;
}
