import React, { useEffect, useState } from 'react';

import ContentBox from '@/components/elements/ContentBox';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import styled from 'styled-components/macro';
import http from '@/api/http';

const Container = styled.div`
    ${tw`flex flex-wrap my-10`};

    & > div {
        ${tw`w-full`};

        ${breakpoint('md')`
            width: calc(50% - 1rem);
        `}

        ${breakpoint('xl')`
            ${tw`w-auto flex-1`};
        `}
    }
`;

export const CoinsContainer = () => {
    const [ totalCoinsEarned, setTotalCoinsEarned ] = useState(0);
    const [ keepAlive, setKeepAlive ] = useState(true);
    const [ failCounter, updateFailCounter ] = useState(0);
    const [ response, setResponse ] = useState(true);

    useEffect(() => {
        request();
    });

    const request = async () => {
        if (keepAlive) {
            if (response) {
                // This makes it unable to send a new request
                // unless you get response from last request
                setResponse(false);
                http.get('/api/client/earn/update').then(resp => {
                    if (resp.status === 200) {
                        setKeepAlive(true);
                        updateFailCounter(0);
                        if (resp.data && resp.data.increment) {
                            const coinsEarned = parseFloat(totalCoinsEarned.toFixed() + resp.data.increment.toFixed());
                            setTotalCoinsEarned(coinsEarned);
                        }
                    } else {
                        updateFailCounter(failCounter + 1);
                    }

                    if (failCounter > 10) {
                        setKeepAlive(false);
                    }
                    setResponse(true);
                }).catch(err => {
                    console.log(err);
                    updateFailCounter(failCounter + 1);
                    if (failCounter > 10) {
                        setKeepAlive(false);
                    }
                    setResponse(true);
                });
            }

            setTimeout(() => request(), 1000 * 20);
        }
    };

    return (
        <PageContentBlock title={'Coins Overview'}>
            <Container>
                <ContentBox css={tw`w-full py-4 px-8 shadow-lg rounded-lg my-20`} title={'Earn Coins'}>
                    <div css={tw`flex justify-center md:justify-end -mt-16`}>
                        <img css={tw`w-20 h-20 object-cover rounded-full shadow-lg`} src="/images/logo.png" />
                    </div>
                    <div css={tw`text-center`}>
                        <h2 css={tw`text-white text-3xl font-bold`}>Netricnodes</h2>
                        <p css={tw`mt-2 text-white`}>
                            You are currently earning Nitros. Keep this page open in your browser to earn more Nitros.
                        </p>
                        <hr css={tw`my-5`} />
                        <p css={tw`mt-2 text-white`}>
                            You have earned
                            <span css={tw`text-green-500 mx-4 font-extrabold text-lg`}>{totalCoinsEarned}</span>
                            Nitros during this session.
                        </p>
                    </div>
                    <hr css={tw`my-5`} />
                    <div css={tw`flex flex-col justify-center mt-4`}>
                        <h3 css={tw`text-2xl font-normal mb-3 leading-7`}>Notes</h3>
                        <p css={tw`text-white`}>- {'Don\'t'} open this page multiple times.</p>
                        <p css={tw`text-white`}>- Make sure you {'don\'t'} have any Ad Blocker activated.</p>
                        <p css={tw`text-white`}>
                            - Make sure you {'didn\'t'} disable WebRTC in your browser
                            settings.
                        </p>
                        <p css={tw`text-white`}>
                            - Make sure you are not opted out of Arc.io. You can check
                            that by clicking the blue icon in the bottom left corner.
                        </p>
                    </div>
                </ContentBox>
            </Container>
        </PageContentBlock>
    );
};

export default CoinsContainer;
