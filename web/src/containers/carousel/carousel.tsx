import React, { JSX } from 'react';
import { v4 as uniqueSlideId } from 'uuid';
import { AutoplayOptions } from 'swiper/types/modules/autoplay';
import { Pagination, Navigation, Autoplay } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import { SwiperOptions } from 'swiper/types/swiper-options';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

interface Props extends React.HTMLProps<HTMLDivElement> {
    autoPlayCarousel?: boolean | AutoplayOptions;
    baseSlidesPerView?: number;
    showNavigation?: boolean;
    spaceBetween?: number;
}

export const Carousel: React.FC<Props> = (props): JSX.Element => {
    const { autoPlayCarousel, baseSlidesPerView = 1, showNavigation = false, spaceBetween = 20, children } = props;
    const sliderClass = uniqueSlideId();
    
    const options: SwiperOptions = {
        modules: [Pagination, Autoplay],
        pagination: { clickable: true, el: `.swiper-2 .swiper-dots._${sliderClass}` },
        autoplay: autoPlayCarousel,
        spaceBetween: spaceBetween || 0,
        slidesPerView: baseSlidesPerView || 1,
        breakpoints: {
            992: { slidesPerView: baseSlidesPerView || 3 },
            768: { slidesPerView: 2 },
            0: { slidesPerView: 1 },
        },
        grabCursor: true,
    };
    
    if (showNavigation) {
        options.modules = [...(options?.modules || []), Navigation];
        options.navigation = {
            nextEl: `.swiper-2 .swiper-next._${sliderClass}`,
            prevEl: `.swiper-2 .swiper-prev._${sliderClass}`,
        };
    }
    
    return (
        <div className='swiper-carousel swiper-testimonials swiper-2'>
            <Swiper {...options}>
                {React.Children.toArray(children).map((child) => (
                    <SwiperSlide key={uniqueSlideId()}>{child}</SwiperSlide>
                ))}
            </Swiper>
            {showNavigation ? (
                <div className='swiper-nav nav-rounded'>
                    <div className={`swiper-prev _${sliderClass}`}>
                        <i className='custom-icon icon chevron-left' />
                    </div>
                    <div className={`swiper-next _${sliderClass}`}>
                        <i className='custom-icon icon chevron-right' />
                    </div>
                </div>
            ) : null}
            <div className={`swiper-dots _${sliderClass}`} />
        </div>
    );
};
