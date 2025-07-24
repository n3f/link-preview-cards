import React from 'react';

export default function Card({ url, title, description, image }) {
    if (!url) return null;
    return (
        <a
            href={url}
            target="_blank"
            rel="noopener noreferrer"
            className="link-preview-card"
        >
            {image && (
                <img
                    src={image}
                    alt=""
                    className="link-preview-card-image"
                />
            )}
            <div className="link-preview-card-content">
                <div className="link-preview-card-title">
                    {title || url}
                </div>
                {description && (
                    <div className="link-preview-card-description">
                        {description}
                    </div>
                )}
                <div className="link-preview-card-url">
                    {url.replace(/^https?:\/\//, '').split('/')[0]}
                </div>
            </div>
        </a>
    );
}
